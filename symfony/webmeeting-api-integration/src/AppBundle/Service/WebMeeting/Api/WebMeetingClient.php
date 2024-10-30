<?php

namespace AppBundle\Service\WebMeeting\Api;

use ReflectionMethod;

/**
 *  Obecná třída pro stranu klienta pro komunikaci se serverem přes API.
 *  Komunikace přes HTTP POST, předávání dat v JSON, symetrické podepisování 
 *  požadavků obsahujících navíc timestamp, symetrické podepisování odpovědí 
 *  obsahujících navíc timestamp. 
 *  
 *  Konkrétní ApiClient rozšíří tuto třídu o svoje public metody, tělo všech   
 *  takových metod může být díky využití reflexe totožné. Příklad:
 *
* public function metoda1($param1, $param2, param3 = null) {
      * return $this->processRequest(__FUNCTION__, func_get_args());
    * }
 *  @author Marek Skalka, skalka@ipcc.cz, 2020 
 */   
abstract class WebMeetingClient {
    
    
  /** Přidělené jméno klienta API */
  protected $api_client;
  
  /** Přidělené sdílené tajemství pro počítání podpisu požadavků */
  protected $api_request_secret;
  
  /** Přidělené sdílené tajemství pro ověřování podpisu odpovědí */
  protected $api_response_secret;

  /** URL endpointu serveru */
  protected $api_url;

  /** @var int */
  protected $client_timestamp_difference_limit = 300; 
      
        
  /**
   * Konstruktor - nastavení parametrů pro komunikaci se serverem.  
   *    
   * @param $api_client string Přidělené jméno klienta API (pro celou aplikaci, CRM...) 
   * @param $api_request_secret string Přidělené sdílené tajemství pro počítání podpisu požadavků
   * @param $api_responese_secret string Přidělené sdílené tajemství pro ověřování podpisu odpovědí
   * @param $api_url string URL endpointu serveru          
   */         
  public function __construct($api_client, $api_request_secret, $api_response_secret, $api_url) {
    $this->api_client = $api_client;
    $this->api_request_secret = $api_request_secret;
    $this->api_response_secret = $api_response_secret;
    $this->api_url = $api_url;
  }    

  
  /** 
   * Připraví a odešle požadavek. Určeno k volání s parametry __FUNCTION__ a
   * func_get_args() ze všech public metod pro konkrétní API, např. takto:

     public function metoda1($param1, $param2, param3 = null) {
       return $this->processRequest(__FUNCTION__, func_get_args());
     }     
   *
   * @param $funcName string Akce, která má být provedena
   * @param $args array Parametry pro tuto akci   
   */  
  protected function processRequest( $funcName, $args ) {    
    return $this->sendRequest($this->composeRequest($funcName, $args));
  } 


  /**
   * Z parametrů $args metody $funcName udělá asociativní pole obsahující 
   * parametry metody ve formátu "název_parametru" => "hodnota parametru";
   * jako nultý parametr se do pole vkládá i "action" => "jméno_metody". 
   * U nevyplněných nepovinných paramterů se do pole zapisuje výchozí hodnota 
   * parametru. 
   * @param $funcName string Jméno funkce (__FUNCTION__)
   * @param $args array Parametry, jak je vrací func_get_args()   
   */  
  protected function composeRequest( $funcName, $args ) {
    $attribute_names = ["action" => $funcName];
    if ( method_exists(get_class($this), $funcName) ) { 
        $fx = new ReflectionMethod(get_class($this), $funcName);
        foreach ($fx->getParameters() as $i => $param){          
          if ($param->isOptional()){
            $attribute_names[$param->getName()] = $param->getDefaultValue();
          } elseif (array_key_exists($param->getPosition(), $args)) { 
            $attribute_names[$param->getName()] = $args[$param->getPosition()];
          } else {            
          }
        }           
     } else {
       throw new \Exception("Unable to compose request (method ".get_class($this)."::$funcName doesn't exist)", 1001); 
     }  
     return $attribute_names;  
  }    

      
  /**
   *  Zformátuje, podepíše a odešle požadavek a vrátí odpověď. 
   *  Při chybě vyhodí výjmku. 
   *     
   *  @param $request string Pole s požadavkem (pod klíčem "action" je název akce, 
   *    pod ostatními klíči jsou pojmenované parametry)       
   */     
  protected function sendRequest($request) {

    // Doplnění požadavku o klienta, timestamp + výpočet hash
    $request['timestamp'] = date("Y-m-d H:i:s");
    $request['client'] = $this->api_client;
    $json_request = json_encode($request);
    $digest = hash_hmac("sha256", $json_request, $this->api_request_secret);
   
    // Odeslání požadavku
//      dd($this->api_url);
    $curl = curl_init($this->api_url);
       
    curl_setopt($curl, CURLOPT_HEADER, true);     
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
      "Content-type: application/json", 
      "Authorization: SaltedChecksum $digest",
      "Content-Length: ".strlen($json_request)   
    ]);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json_request);    
    $headers = [];
    curl_setopt($curl, CURLOPT_HEADERFUNCTION,
      function($curl, $header) use (&$headers) { 
        // this function is called by curl for each header received
        $len = strlen($header);
        $header = explode(':', $header, 2);
        if (count($header) < 2) {           
          return $len; // ignore invalid headers
        }
        $headers[strtolower(trim($header[0]))] = trim($header[1]);
        return $len;
      }
    );
    
    // Zpracování odpovědi 
    $response = curl_exec($curl); 
    
    if ($curl_error = curl_error($curl)) {
      // Selhání CURL
      throw new \Exception("Unable to connect to $this->api_url (Curl error ".curl_errno($curl).": $curl_error)", 1002);
    }

    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if ( $status != 200 && $status != 201 && $status != 400) {
      throw new \Exception("Unable to get response from $this->api_url (HTTP code $status)", 1003);
    }
    
   	$headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
  	$body = substr($response, $headerSize);
    curl_close($curl);
 
    $result = json_decode($body, true);
    
    if ($err = json_last_error() !== JSON_ERROR_NONE) {
      // Tělo odpovědi není platný JSON
      throw new \Exception("Unable to decode server response (JSON error $err, raw response $body)", 1009);
    }

    if ( $status == 400 && isset($result["error"])) {
      throw new \Exception("Server error: $result[error]", isset($result["code"])?$result["code"]:1004);
    }
    if ( $status == 400 ) {
      throw new \Exception("Bad Request response received (HTTP code 400)", 1005);
    }    
    
    // Porovnat headers["authorization"] s očekávaným podpisem odpovědi                
    $digestComputed = hash_hmac("sha256", $body, $this->api_response_secret);        
    if (preg_match('/SaltedChecksum (.*)/', $headers["authorization"], $matches)) {
      $digestInResponse = $matches[1];
    } else {
      $digestInResponse = "";
    }    
    if (!hash_equals($digestComputed, $digestInResponse)) {
      // Vypočítaný a příchozí hash odpovědi neodpovídá 
      throw new \Exception("Unable to validate server response (Invalid digest $digestInResponse)", 1006);
    } 
        
    if (!isset($result["server_timestamp"])) {      
      // + oveření vráceného timestapmu - zatím neimplementováno.
      throw new \Exception("Unable to check timestamp (no server timestamp provided), ", 1007);       
    }    
    $now = date("Y-m-d H:i:s");
    $client_datetime = strtotime($now); 
    $response_datetime = strtotime($result["server_timestamp"]); 
    if (abs($client_datetime - $response_datetime) >= $this->client_timestamp_difference_limit) {
      throw new \Exception("Unable to accept server timestamp (client time is $now)", 1008);       
    }
        
    return $result["response"];  
  }      
  
} // of class
  
  

