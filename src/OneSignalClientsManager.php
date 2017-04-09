<?php 

namespace Berkayk\OneSignal;

class OneSignalClientsManager{

  private $clients;

  public function __construct($clients){
        $this->clients = $clients;
    }
  
  public function setClients($clients){
    $this->clients = $clients;
  }
  
  //Get all the clients
  public function getClients(){
      return $this->clients;
  }
  
  //Get one client 
  public function getClient($clientName){
      return $this->clients[$clientName];
  }
}
