<?php

namespace Berkayk\OneSignal;

use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Http\Client\Common\HttpMethodsClient as HttpClient;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use OneSignal\Config;
use OneSignal\Devices;
use OneSignal\OneSignal;

class OneSignalClient
{
    protected $client;
    protected $config;

    public function __construct($appId, $restApiKey, $userKey)
    {
        $this->config = new Config();
        $this->config->setApplicationId($appId);
        $this->config->setApplicationAuthKey($restApiKey);
        $this->config->setUserAuthKey($userKey);

        $guzzle = new GuzzleClient([
        ]);

        $httpClient = new HttpClient(new GuzzleAdapter($guzzle), new GuzzleMessageFactory());
        $client = new OneSignal($this->config, $httpClient);
    }

    public function test() {
        return $this->client. " ".$this->config;
    }
}