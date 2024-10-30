<?php

namespace AppBundle\Service\WebMeeting\Api;

/**
 *  Klientská třída pro ovládání aplikace Webmeeting z externích aplikací, CRM atd., přes API.
 *
 *  Poznámky k hierarchii účtů:    
 *  Jedním klientem API může být celá externí aplikace (CRM), i když je třeba cloudová a sdílená více 
 *  firmami, a s jedněmi přístupovými údaji CRM ve Webmeetingu pracuje jménem všech těchto firem. 
 *  A obráceně, každá z těchto firem má ve Webmeetingu svůj samostatný klientský účet, i když jejím 
 *  jménem jedná jediné CRM.
 *  
 *  Klienti CRM mohou souběžne (ale také vůbec nemusí) využívat přístup přes administraci Webmeetingu 
 *  na https://admin.webmeeting.cz    
 *       
 */   
class WebMeetingApiClient extends WebMeetingClient implements WebMeetInterface {
  
  public function createAccount($name, $login, $email, $password) {
    return $this->processRequest(__FUNCTION__, func_get_args()); 
  }

  public function updateMeeting($login, $meetingId, $name, $time_begin, $speaker_name, $description, $type) {   
    return $this->processRequest(__FUNCTION__, func_get_args()); 
  }

  public function createMeeting($login, $name, $time_begin, $speaker_name, $description, $type) {    
    return $this->processRequest(__FUNCTION__, func_get_args()); 
  }

  public function deleteMeeting($login, $meetingId) {  
    return $this->processRequest(__FUNCTION__, func_get_args()); 
  }

  public function configureMeeting($login, $meetingId, $options) {  
    return $this->processRequest(__FUNCTION__, func_get_args()); 
  }

  public function getMeetings($login, $meetingId) {  
    return $this->processRequest(__FUNCTION__, func_get_args()); 
  }

  public function createParticipant($login, $number, $surname, $firstname, $email) {  
    return $this->processRequest(__FUNCTION__, func_get_args()); 
  }

  public function updateParticipant($login, $participantId, $number, $surname, $firstname, $email) {  
    return $this->processRequest(__FUNCTION__, func_get_args()); 
  }  

  public function deleteParticipant($login, $participant_id) {  
    return $this->processRequest(__FUNCTION__, func_get_args()); 
  }

  public function getParticipants($login, $participant_id) {  
    return $this->processRequest(__FUNCTION__, func_get_args()); 
  }

  public function setAccess($login, $meetingId, $participantId, $level) {  
    return $this->processRequest(__FUNCTION__, func_get_args()); 
  }
  
  public function getAccess($login, $meetingId, $participantId) {  
    return $this->processRequest(__FUNCTION__, func_get_args()); 
  }
 
  public function importParticipants($login, $meetingId, $participants, $access_level) {  
    return $this->processRequest(__FUNCTION__, func_get_args()); 
  }
  
  public function setMeetingEmpty($login, $meetingId) {  
    return $this->processRequest(__FUNCTION__, func_get_args()); 
  }

  public function sendInvitations($login, $meetingId, $mode, $filter, $body) {  
    return $this->processRequest(__FUNCTION__, func_get_args()); 
  }
  
  public function getModeratorAccess($login, $meetingId) {  
    return $this->processRequest(__FUNCTION__, func_get_args()); 
  }
      
  public function getModeratorEnterURL($login, $meetingId, $moderatorName, $html5client) {  
    return $this->processRequest(__FUNCTION__, func_get_args()); 
  }

  public function getParticipantEnterURL($login, $meetingId, $participantId, $html5client) {  
    return $this->processRequest(__FUNCTION__, func_get_args()); 
  }

  public function importParticipantAndGetEnterURL($login, $meetingId, $participants, $access_level, $html5client) {  
    return $this->processRequest(__FUNCTION__, func_get_args()); 
  }    

  public function getRecordings($login, $meetingId) {  
    return $this->processRequest(__FUNCTION__, func_get_args()); 
  }

  public function getChatRecordings($login, $meetingId) {  
    return $this->processRequest(__FUNCTION__, func_get_args()); 
  }
  
  public function isLoginUsed($login) {  
    return $this->processRequest(__FUNCTION__, func_get_args()); 
  }
  
  public function setAccessLevel($login, $accessLevel, $days) {  
    return $this->processRequest(__FUNCTION__, func_get_args()); 
  }  
      
} // of class
  
  

