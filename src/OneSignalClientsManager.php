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
  
  public function getClients(){
      return $this->clients;
  }
}