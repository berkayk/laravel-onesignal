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
        return "APP ID: ".$this->appId." REST: ".$this->restApiKey;
    }

    private function requiresAuth() {
        $this->headers['headers']['Authorization'] = 'Basic '.$this->restApiKey;
    }

    private function usesJSON() {
        $this->headers['headers']['Content-Type'] = 'application/json';
    }

    public function sendNotificationToUser($message, $userId, $url = null, $data = null) {
        $contents = array(
            "en" => $message
        );

        $params = array(
            'app_id' => $this->appId,
            'contents' => $contents,
            'include_player_ids' => array($userId)
        );

        if (isset($url)) {
            $params['url'] = $url;
        }

        if (isset($data)) {
            $params['data'] = $data;
        }

        $this->sendNotificationCustom($params);
    }

    public function sendNotificationToAll($message, $url = null, $data = null) {
        $contents = array(
            "en" => $message
        );

        $params = array(
            'app_id' => $this->appId,
            'contents' => $contents,
            'included_segments' => array('All')
        );

        if (isset($url)) {
            $params['url'] = $url;
        }

        if (isset($data)) {
            $params['data'] = $data;
        }

        $this->sendNotificationCustom($params);
    }

    public function sendNotificationToSegment($message, $segment, $url = null, $data = null) {
        $contents = array(
            "en" => $message
        );

        $params = array(
            'app_id' => $this->appId,
            'contents' => $contents,
            'included_segments' => [$segment]
        );

        if (isset($url)) {
            $params['url'] = $url;
        }

        if (isset($data)) {
            $params['data'] = $data;
        }

        $this->sendNotificationCustom($params);
    }

    /**
     * Send a notification with custom parameters defined in
     * https://documentation.onesignal.com/v2.0/docs/notifications-create-notification
     * @param array $parameters
     * @return mixed
     */
    public function sendNotificationCustom($parameters = []){
        $this->requiresAuth();
        $this->usesJSON();

        // Make sure to use app_id
        $parameters['app_id'] = $this->appId;

        // Make sure to use included_segments
        if (empty($parameters['included_segments']) && empty($parameters['include_player_ids'])) {
            $parameters['included_segments'] = ['all'];
        }

        $this->headers['body'] = json_encode($parameters);
        $this->headers['verify'] = false;
        return $this->post("notifications");
    }

    public function post($endPoint) {
        return $this->client->post(self::API_URL."/".$endPoint, $this->headers);
    }

}