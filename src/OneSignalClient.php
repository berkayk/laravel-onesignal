<?php

namespace Berkayk\OneSignal;

use GuzzleHttp\Client;

class OneSignalClient
{
    const API_URL = "https://onesignal.com/api/v1";
    private $client;
    private $headers;
    private $appId;
    private $restApiKey;
    private $userAuthKey;

    public function __construct($appId, $restApiKey, $userAuthKey)
    {
        $this->appId = $appId;
        $this->restApiKey = $restApiKey;
        $this->userAuthKey = $userAuthKey;

        $this->client = new Client();
        $this->headers = ['headers' => []];
    }

    public function testCredentials() {
        return "APP ID".$this->appId." REST: ".$this->restApiKey;
    }

    private function addApiKey(){
        $this->headers['headers']['Authorization'] = 'Basic '.$this->restApiKey;
    }

    private function addUser(){
        $this->headers['headers']['Authorization'] = 'Basic '.$this->userAuthKey;
    }

    public function addJson(){
        $this->headers['headers']['Content-Type'] = 'application/json';
    }

    public function createNotification($parameters = []){
        $this->headers['headers']['Authorization'] = 'Basic '.$this->restApiKey;
        $this->headers['headers']['Content-Type'] = 'application/json';
        $this->headers['body'] = json_encode($parameters);
        return $this->post("notifications");
    }

    public function post($endPoint) {
        return $this->client->post(self::API_URL."/".$endPoint);
    }

}