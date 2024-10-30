<?php

namespace AppBundle\Service\WebMeeting\Api;
/** 
 *  Interface pro Webmeeting API - obsahuje metody, přes které lze Webmeeting
 *  ovládat vzdáleně z jiné aplikace.
 * 
 *  Tento interface je implementován klientem (stub WebmeetingApiClient) stejně 
 *  jako serverem (skeleton WebmeetingServer). 
 *  
 *  @version 20201109  
 *  
 */

interface WebMeetInterface {

  /** Nepřihlášen (vztah k účastníka k setkání) */
  const ACCESS_NONE = 0;       
  
  /** Přihlášen pouze na on-line setkání (vztah k účastníka k setkání) */
  const ACCESS_ONLINE_ONLY = 1;
  
  /** Přihlášen pouze k prohlížení záznamu (vztah k účastníka k setkání) */
  const ACCESS_RECORD_ONLY = 2;
  
  /** Přihlášen na on-line setkání i k prohlížení záznamu (vztah k účastníka k setkání) */
  const ACCESS_ONLINE_AND_RECORD = 3;
  

  /**
   *  Vytvoří nový účet (nového uživatele) ve Webmeetingu - vyhrazeno pro vnitřní 
   *  použití a pro partnery. Při nastavování  hesla do Webmeetingu nepoužívejte 
   *  stejné heslo jako do systému, ze kterého API používáte; je na business 
   *  logice, dáte-li uživateli jeho heslo nějak vědět. Pro komunikaci přes 
   *  API toto heslo není potřeba, požadavky se podepisují tajemstvím klienta API 
   *  (systému partnera). 
   *           
   *  @param $name string Jméno nového uživatele
   *  @param $login string Přihlašovací jméno nového uživatele, musí být unikátní
   *  @param $email string Emailová adresa klienta
   *  @param $password string Hash hesla pro konkrétního klienta, lze použít: md5() - bude přehashováno  
   */   
  function createAccount($name, $login, $email, $password);

  /**
   *  Upraví již vytvořené setkání. Úpravy jsou omezené stejně jako při přístupu 
   *  přes webové rozhraní, již započaté setkání nelze upravovat.   
   *          
   *  @param $login string Login uživatele, jemuž setkání patří.
   *  @param $meetingId int ID setkání   
   *  @param $name string Nový název setkání
   *  @param $time_begin string Nový čas začátku setkání ve formátu "d.m.Y H:i". Pokud se zadá prázdný (nebo v blízké minulosti), bude považován za TEĎ HNED.
   *  @param $speaker_name string Nové jméno přednášejícího
   *  @param $description string Nový popis setkání 
   *  @param $type int Nový typ setkání (konstanty viz dokumentace)
   */      
  function updateMeeting($login, $meetingId, $name, $time_begin, $speaker_name, $description, $type /*, $is_public, $public_capacity, $public_price*/);
   /*  @param $is_public int Umožnit na setkání přihlašování veřejnosti (0 ne, 1 ano, 2 ano + umístit do veřejného seznamu) 
   *  @param $public_capacity int Kapacita pro veřejnost
   *  @param $public_price int Cena pro veřejnost, -1 = placený v režii pořadatele    
   */     

  /**
   *  Vytvoří nové setkání (poradu, meeting nebo webinář). V odpovědi vrací 
   *  ID setkání. 
   *         
   *  @param $login string Login uživatele, pro kterého setkání vytváříme. 
   *  @param $name string Název setkání
   *  @param $time_begin string Čas začátku setkání ve formátu "d.m.Y H:i". Pokud se zadá prázdný (nebo v blízké minulosti), bude považován za TEĎ HNED. 
   *  @param $speaker_name string Jméno přednášejícího
   *  @param $description string Popis setkání 
   *  @param $type int Typ setkání (konstanty viz dokumentace)
   */      
  function createMeeting($login, $name, $time_begin, $speaker_name, $description, $type /*, $is_public, $public_capacity, $public_price*/);

  /** 
   *  Smaže již vytvořené setkání. Smazání je omezené stejně jako při přístupu
   *  přes webové rozhraní, již započaté setkání nelze smazat.
   *        
   *  @param $login string Login uživatele, jemuž setkání patří.
   *  @param $meetingId int ID setkání   
   */    
  function deleteMeeting($login, $meetingId);

  /** 
   *  Nastaví další parametry pro již vytvořené setkání.   
   *     
   *  @param $login string Login uživatele, jemuž setkání patří.
   *  @param $meetingId int ID setkání   
   *  @param $options array Pole s konfiguračními parametry (klíč-hodnota), povolené klíče jsou:<ul>
        <li> auto_start_recording (začít automaticky nahrávání, aktuálně nepodporováno BBB)
        <li> is_public (Umožnit na setkání přihlašování veřejnosti - 0 ne, 1 ano, 2 ano + umístit do veřejného seznamu),
        <li> public_capacity (Kapacita pro veřejnost),
        <li> public_price (Cena pro veřejnost, -1 = placený v režii pořadatele)</ul>      
   */ 
  function configureMeeting($login, $meetingId, $options);

  /** 
   *  Vrací seznam všech existujících setkání.
   *        
   *  @param $login string Login uživatele, jemuž setkání patří.
   *  @param $meetingId ?int Volitelné: ID setkání - je-li nastaveno, vrací pouze jedno setkání s tímto ID.   
   */ 
  function getMeetings($login, $meetingId);

  /**
   *  Vytvoří účastníka - t.j. někoho, kdo se může účastnit setkání. V odpovědi 
   *  vrací ID účastníka (jako $participantId).
   *  
   *  @param $login string Login uživatele, k němuž účastník patří.     
   *  @param $number string registrační číslo (smí být prázdné)
   *  @param $surname string příjmení 
   *  @param $firstname string jméno 
   *  @param $email string platná e-mailová adresa   
   */   
  function createParticipant($login, $number, $surname, $firstname, $email);

  /**
   *  Upraví existujícího účastníka.
   *  
   *  @param $login string Login uživatele, k němuž účastník patří.     
   *  @param $participantId int ID účastníka   
   *  @param $number string registrační číslo, smí být prázdné
   *  @param $surname string příjmení 
   *  @param $firstname string jméno 
   *  @param $email string platná e-mailová adresa   
   */  
  function updateParticipant($login, $participantId, $number, $surname, $firstname, $email);  

  /**
   *  Smaže existujícího účastníka. 
   *  
   *  @param $login string Login uživatele, k němuž účastník patří.     
   *  @param $participant_id int ID účastníka   
   */  
  function deleteParticipant($login, $participant_id);

  /** 
   *  Vrací seznam všech existujících účastníků.
   *        
   *  @param $login string Login uživatele, k němuž účastnci patří.   
   *  @param $participant_id ?int Volitelně: ID účastníka, je-li nastaveno, vrací pouze jednoho účastníka s tímto ID.   
   */ 
  function getParticipants($login, $participant_id);

  /**
   *  Nastaví účastníkovi přístup k setkání.
   *  
   *  @param $login string Login uživatele
   *  @param $meetingId int ID setkání   
   *  @param $participantId int ID účastníka  
   *  @param $access_level int Nová úroveň přístupu (ACCESS_NONE, ACCESS_ONLINE_ONLY, ACCESS_RECORD_ONLY, ACCESS_ONLINE_AND_RECORD)   
   */     
  function setAccess($login, $meetingId, $participantId, $level);
  
  /**
   *  Zjistí aktuální nastavení úrovně přístupu k setkání (pro jednoho zvoleného a nebo pro všechny 
   *  přihlášené účastníky).
   *  
   *  @param $login string Login uživatele
   *  @param $meetingId int ID setkání   
   *  @param $participantId int ID účastníka - volitelné - může být null, pak vrací všechny účastníky s jiným právem než ACCESS_NONE
   *  @returns tiket (pro zadaný atribut $participantId) nebo pole tiketů indexované participantId (jinak), tiket obsahuje 
   *     "participantId", "meetingId", "isAllowedOnline" (povolení vstupu online), "isAllowedRecord" (povolení vstupu k záznamu), 
   *     "token", "onlineInvitationSent" a "recordInvitationSent" (čas odeslání pozvánek)   
   */     
  function getAccess($login, $meetingId, $participantId);
 
  /**
   *  Naimportuje účastníky a dle požadavku je přiřadí k existujícímu setkání. Pokud už identický účastník 
   *  existuje, není vytvářen duplicitně. Pokud má už účastník nějaká práva k setkání, úroveň přístupu 
   *  k setkání, která bude účastníkům je mu pouze přidávána, ale nikdy ubírána.     
   *          
   *  @param $login string Login uživatele, jemuž setkání patří.
   *  @param $meetingId int ID setkání, může být null, v takovém případě jsou účastníci pouze 
   *    naimportování, ale nejsou přiřazeni k žádnému setkání
   *  @param $participants array Pole účastníků, každá položka pole musí být asociativní pole s klíči 
   *    'number' (reg. číslo, smí být prázdné), 'surname' (příjmení), 
   *    'firstname' (jméno) a 'email' (musí být platnou e-mailovou adresou).
   *  @param $access_level int Úroveň přístupu k setkání, která bude účastníkům přidána (ACCESS_ONLINE_ONLY, ACCESS_RECORD_ONLY, ACCESS_ONLINE_AND_RECORD)
   *  @returns array Pole, kde klíčem je číslo záznamu ze vstupu $participants a hodnotou ID vloženého (nebo již existujícího) účastníka                                
   */  
  function importParticipants($login, $meetingId, $participants, $access_level);

  /**
   * Odebere všechny přístupy k setkání s daným $meetingId - myšleno jako reset 
   * přihlášených účastníků před novým importParticipants()    
   * @param $login string Login uživatele, jemuž setkání patří.   
   * @param $meetingId int ID setkání
   */   
  function setMeetingEmpty($login, $meetingId);

  /**
   *  Rozešle jednorázově pozvánky k setkání (s defaultním textem pozvánky).
   *  @param $login string Login uživatele, jemuž setkání patří.   
   *  @param $meetingId int ID setkání   
   *  @param $mode int Druh pozvánek: 0 pro pozvánky na online, 1 pro pozvánky k záznamu
   *  @param $filter int Filtr (0 = všem, 1 = jen dosud nepozvaným)
   *  @param $body string Tělo pozvánky, musí obsahivat /%URL%/ pro vložení odkazu pro vstup. Alternativně, pokud je $body prázdné, použije Webmeeting standardní text pozvánky.
   */ 
  function sendInvitations($login, $meetingId, $mode, $filter, $body);
  
  /**
   *  Vrátí URL vstupní stránky pro moderátora a vstupní kód pro přihlášení moderátora k danému setkání, jako pole
   *  s klíči "url" a "moderatorCode".   
   *
   *  @param $login string Login uživatele, jemuž setkání patří.   
   *  @param $meetingId int ID setkání   
   */    
  function getModeratorAccess($login, $meetingId);

  /**
   *  Vrátí přímo URL místnosti pro moderátora k danému setkání.
   *
   *  @param $login string Login uživatele, jemuž setkání patří.   
   *  @param $meetingId int ID setkání   
   *  @param $moderatorName string Jméno moderátora  
   *  @param $html5client bool Použít html5 klienta?, výchozí je true
   *  @return string
   */          
  function getModeratorEnterURL($login, $meetingId, $moderatorName, $html5client);

  /**
   *  Vrátí přímo URL místnosti pro účastníka k danému setkání.
   *
   *  @param $login string Login uživatele, jemuž setkání patří.  
   *  @param $meetingId int ID setkání   
   *  @param $participantId int ID účastníka    
   *  @param $html5client bool Použít html5 klienta?, výchozí je true
   *  @returnss string
   */ 
  function getParticipantEnterURL($login, $meetingId, $participantId, $html5client);

  /**
   *  Provede import účastníků (pokud účastník neexistuje, vytvoří se, pokud 
   *  není k setkání přiřazen, přiřadí se) a rovnou vrátí unikátní URL místnosti 
   *  pro vstup těchto účastníků k danému setkání. 
   *  @param $login string Login uživatele, jemuž setkání patří.  
   *  @param $meetingId int ID setkání   
   *  @param $participants array účastník - musí být asociativní pole s klíči 
   *                           'number' (reg. číslo, smí být prázdné), 'surname' (příjmení), 
   *                           'firstname' (jméno) a 'email' (musí být platnou e-mailovou adresou).
   *  @param $access_level int Úroveň přístupu (ACCESS_NONE, ACCESS_ONLINE_ONLY, ACCESS_RECORD_ONLY, ACCESS_ONLINE_AND_RECORD)   
   *  @param $html5client bool Použít html5 klienta?
   *  @returns array Pole, kde klíčem je číslo záznamu ze vstupu $participants a hodnotou URL pro vstup účastníka na setkání                                
   */    
  function importParticipantAndGetEnterURL($login, $meetingId, $participants, $access_level, $html5client);

  /**
   *  Vrátí seznam nahrávek k danému setkání vč. odkazů pro jejich přehrání.
   *          
   *  @param $login string Login uživatele, jemuž setkání patří.
   *  @param $meetingId int ID setkání
   *  @returns array Pole s informacemi o nahrávkách (jedno setkání může mít více nahrávek), indexováno kódem nahrávky,
   *    obsahuje hodnoty indexované url (odkaz na nahrávku), length (délka v minutách) a startTime (čas začátku ve formátu 06.11.2019 12:59:56).                                     
   */
  function getRecordings($login, $meetingId);

  /**
   *  Vrátí výpis ze zaznamenaného chatu k danému setkání. Podmínkou je, že pro setkání byl pořízen záznam.
   *          
   *  @param $login string Login uživatele, jemuž setkání patří.
   *  @param $meetingId int ID setkání
   *  @returns array Pole obsahující záznamy chatu k jednotlivým nahrávkám (jedno setkání může mít více nahrávek), 
   *    v první úrovni indexováno kódem nahrávky, ve druhé úrovni jednotlivé zprávy indexovány od 0,
   *    v poslední úrovni obsahuje hodnoty indexované time (čas zprávy od začátku nahrávky), name (jméno posluchače) a message (text vlastní zrpávy).                                     
   */        
  function getChatRecordings($login, $meetingId);
  

  
  /**
   * Zjistí, zda existuje uživatel s daným loginem - vyhrazeno pro vnitřní 
   * použití a pro partnery.
   */
  function isLoginUsed($login);
  

  /**
   * Přenastaví uživateli placený program - vyhrazeno pro vnitřní použití a pro partnery.
   * 
   * <ul>
   *   <li> pokud login neexistuje, skončí s chybou,
   *   <li> pokud je novy program $accessLevel stejný jako starý program, prodlouží tento program o $days dni
   *   <li> pokud je novy program $accessLevel jiný než starý program (a nebo pokud starý program už vypršel), nastaví nový program od teď na $days dni
   *   </ul>      
   */
  function setAccessLevel($login, $accessLevel, $days);
  
  
           
} // CLASS  