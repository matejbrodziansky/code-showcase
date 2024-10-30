<?php

/* ===== Includes ===== */
  require __DIR__."/ApiClient.php";  // Implementace obecného klienta
  require __DIR__."/WebmeetingInterface.php";  // Popis rozhraní aplikace Webmeeting
  require __DIR__."/WebmeetingApiClient.php";  // Implementace klienta pro aplikaci Webmeeting

/* ===== Konfigurace ===== */
  define('WM_API_CLIENT', '', true); // Přihlašovací jméno 
  define('WM_API_URL', 'https://admin.webmeeting.cz/api', true); // Adresa serveru
  define('WM_API_REQUEST_SECRET', '', true); // Tajemství pro podepisování a ověřování zpráv
  define('WM_API_RESPONSE_SECRET', '', true); // Tajemství pro podepisování a ověřování zpráv

/**
 * Ukázkový script, který demonstruje možnosti Webmeeting API, tedy ovládání
 * aplikace Webmeeting od společnosti IPCC z jiné aplikace.  
 * 
 * <b>Jak se to používá?</b>
 *   
 *- Includujte ApiClient.php, WebmeetingInterface.php a WebmeetingApiClient.php</li>
 *
 *- Vytvořte instanci WebmeetingApiClient - jako parametry budete potřebovat svoje 
 *  jméno klienta, adresu webmeeeting serveru a 2 sdílená tajemství pro ověřování zpráv.</li>
 *
 *- Používejte metody WebmeetingApiClient k ovládání aplikace Webmeeting - dokumentaci 
 *  najdete u WebmeetingInterface</li>  
 * 
 * @see WebmeetingInterface.php 
 * @version 20201002
 *   
 */    
class DemoClient {

  /** Spustí demonstrační script využívající jednotlivé operace Webmeeting API. 
   *  Pokud chcete API vyzkoušet, mrkněte se na zdrojový kód metody runDemo()
   *  a upravujte ho podle potřeby. */
  public function runDemo() {
    
    /* ===== Použití ===== */
    $client = new WebmeetingApiClient(WM_API_CLIENT, WM_API_REQUEST_SECRET, WM_API_RESPONSE_SECRET, WM_API_URL);
    
    try {  
      echo "<h1>Vypisuji všechna setkání…</h1>";  
      $response = $client->getMeetings(WM_API_CLIENT, NULL);
      echo "OK, odpověď = ".print_r($response, true).'<br>';
    } catch (\Exception $e) {
      echo "<b>Došlo k neočekávané výjimce</b> (".$e->getCode()."): ".$e->getMessage()."<br>";
    }        
  } // runDemo()  

} // class



/* ===== Spuštění ====== */
if (WM_API_CLIENT == "") {
  echo "<h1>Nastavte nejprve své přístupové údaje</h1><p>Více v ukázkovém souboru ".__FILE__."</h1>";
  die();
}
$demoClient = new DemoClient();
$demoClient->runDemo(); 