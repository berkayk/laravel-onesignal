<?php

namespace Berkayk\OneSignal;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request as Psr7Request;
use GuzzleHttp\Psr7\Response as Psr7Response;

class OneSignalClient
{
    const API_URL = "https://onesignal.com/api/v1";

    const ENDPOINT_NOTIFICATIONS = "/notifications";
    const ENDPOINT_PLAYERS = "/players";
    const ENDPOINT_APPS = "/apps";

    protected $client;
    protected $headers;
    protected $appId;
    protected $restApiKey;
    protected $userAuthKey;
    protected $additionalParams;

    /**
     * @var bool
     */
    public $requestAsync = false;

    /**
     * @var int
     */
    public $maxRetries = 2;

    /**
     * @var int
     */
    public $retryDelay = 500;

    /**
     * @var Callable
     */
    private $requestCallback;

    /**
     * Turn on, turn off async requests
     *
     * @param bool $on
     * @return $this
     */
    public function async($on = true)
    {
        $this->requestAsync = $on;
        return $this;
    }

    /**
     * Callback to execute after OneSignal returns the response
     * @param Callable $requestCallback
     * @return $this
     */
    public function callback(Callable $requestCallback)
    {
        $this->requestCallback = $requestCallback;
        return $this;
    }

    /**
     * @param $appId
     * @param $restApiKey
     * @param $userAuthKey
     * @param int $guzzleClientTimeout
     */
    public function __construct($appId, $restApiKey, $userAuthKey, $guzzleClientTimeout = 0)
    {
        $this->appId = $appId;
        $this->restApiKey = $restApiKey;
        $this->userAuthKey = $userAuthKey;

        $this->client = new Client([
            'handler' => $this->createGuzzleHandler(),
            'timeout' => $guzzleClientTimeout,
        ]);
        $this->headers = ['headers' => []];
        $this->additionalParams = [];
    }

    private function createGuzzleHandler() {
        return tap(HandlerStack::create(new CurlHandler()), function (HandlerStack $handlerStack) {
            $handlerStack->push(Middleware::retry(function ($retries, Psr7Request $request, Psr7Response $response = null, RequestException $exception = null) {
                if ($retries >= $this->maxRetries) {
                    return false;
                }

                if ($exception instanceof ConnectException) {
                    return true;
                }

                if ($response && $response->getStatusCode() >= 500) {
                    return true;
                }

                return false;
            }), $this->retryDelay);
        });
    }

    public function testCredentials() {
        return "APP ID: ".$this->appId." REST: ".$this->restApiKey;
    }

    private function requiresAuth() {
        $this->headers['headers']['Authorization'] = 'Basic '.$this->restApiKey;
    }

    private function requiresUserAuth() {
        $this->headers['headers']['Authorization'] = 'Basic '.$this->userAuthKey;
    }

    private function usesJSON() {
        $this->headers['headers']['Content-Type'] = 'application/json';
    }

    public function addParams($params = [])
    {
        $this->additionalParams = $params;

        return $this;
    }

    public function setParam($key, $value)
    {
        $this->additionalParams[$key] = $value;

        return $this;
    }

    public function sendNotificationToUser($message, $userId, $url = null, $data = null, $buttons = null, $schedule = null, $headings = null, $subtitle = null) {
        $contents = array(
            "en" => $message
        );

        $params = array(
            'app_id' => $this->appId,
            'contents' => $contents,
            'include_player_ids' => is_array($userId) ? $userId : array($userId)
        );

        if (isset($url)) {
            $params['url'] = $url;
        }

        if (isset($data)) {
            $params['data'] = $data;
        }

        if (isset($buttons)) {
            $params['buttons'] = $buttons;
        }

        if(isset($schedule)){
            $params['send_after'] = $schedule;
        }

        if(isset($headings)){
            $params['headings'] = array(
                "en" => $headings
            );
        }
        
        if(isset($subtitle)){
            $params['subtitle'] = array(
                "en" => $subtitle
            );
        }

        $this->sendNotificationCustom($params);
    }

    /**
     * @param $message
     * @param $userId
     * @param null $url
     * @param null $data
     * @param null $buttons
     * @param null $schedule
     * @param null $headings
     * @param null $subtitle
     */
    public function sendNotificationToExternalUser($message, $userId, $url = null, $data = null, $buttons = null, $schedule = null, $headings = null, $subtitle = null) {
        $contents = array(
            "en" => $message
        );

        $params = array(
            'app_id' => $this->appId,
            'contents' => $contents,
            'include_external_user_ids' => is_array($userId) ? $userId : array($userId)
        );

        if (isset($url)) {
            $params['url'] = $url;
        }

        if (isset($data)) {
            $params['data'] = $data;
        }

        if (isset($buttons)) {
            $params['buttons'] = $buttons;
        }

        if(isset($schedule)){
            $params['send_after'] = $schedule;
        }

        if(isset($headings)){
            $params['headings'] = array(
                "en" => $headings
            );
        }

        if(isset($subtitle)){
            $params['subtitle'] = array(
                "en" => $subtitle
            );
        }

        $this->sendNotificationCustom($params);
    }
    public function sendNotificationUsingTags($message, $tags, $url = null, $data = null, $buttons = null, $schedule = null, $headings = null, $subtitle = null) {
        $contents = array(
            "en" => $message
        );

        $params = array(
            'app_id' => $this->appId,
            'contents' => $contents,
            'filters' => $tags,
        );

        if (isset($url)) {
            $params['url'] = $url;
        }

        if (isset($data)) {
            $params['data'] = $data;
        }

        if (isset($buttons)) {
            $params['buttons'] = $buttons;
        }

        if(isset($schedule)){
            $params['send_after'] = $schedule;
        }

        if(isset($headings)){
            $params['headings'] = array(
                "en" => $headings
            );
        }
        
        if(isset($subtitle)){
            $params['subtitle'] = array(
                "en" => $subtitle
            );
        }

        $this->sendNotificationCustom($params);
    }

    public function sendNotificationToAll($message, $url = null, $data = null, $buttons = null, $schedule = null, $headings = null, $subtitle = null) {
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

        if (isset($buttons)) {
            $params['buttons'] = $buttons;
        }

        if(isset($schedule)){
            $params['send_after'] = $schedule;
        }

        if(isset($headings)){
            $params['headings'] = array(
                "en" => $headings
            );
        }
        
        if(isset($subtitle)){
            $params['subtitle'] = array(
                "en" => $subtitle
            );
        }

        $this->sendNotificationCustom($params);
    }

    public function sendNotificationToSegment($message, $segment, $url = null, $data = null, $buttons = null, $schedule = null, $headings = null, $subtitle = null) {
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

        if (isset($buttons)) {
            $params['buttons'] = $buttons;
        }

        if(isset($schedule)){
            $params['send_after'] = $schedule;
        }

        if(isset($headings)){
            $params['headings'] = array(
                "en" => $headings
            );
        }
        
        if(isset($subtitle)){
            $params['subtitle'] = array(
                "en" => $subtitle
            );
        }

        $this->sendNotificationCustom($params);
    }

    public function deleteNotification($notificationId, $appId = null) {
        $this->requiresAuth();

        if(!$appId)
            $appId = $this->appId;
        $notificationCancelNode = "/$notificationId?app_id=$this->appId";
        return $this->delete(self::ENDPOINT_NOTIFICATIONS . $notificationCancelNode);

    }

    /**
     * Send a notification with custom parameters defined in
     * https://documentation.onesignal.com/reference#section-example-code-create-notification
     * @param array $parameters
     * @return mixed
     */
    public function sendNotificationCustom($parameters = []){
        $this->requiresAuth();
        $this->usesJSON();

        if (isset($parameters['api_key'])) {
            $this->headers['headers']['Authorization'] = 'Basic '.$parameters['api_key'];
        }

        // Make sure to use app_id
        if (!isset($parameters['app_id'])) {
            $parameters['app_id'] = $this->appId;
        }

        // Make sure to use included_segments
        if (empty($parameters['included_segments']) && empty($parameters['include_player_ids']) && empty('include_external_user_ids')) {
            $parameters['included_segments'] = ['All'];
        }

        $parameters = array_merge($parameters, $this->additionalParams);

        $this->headers['body'] = json_encode($parameters);
        $this->headers['buttons'] = json_encode($parameters);
        $this->headers['verify'] = false;
        return $this->post(self::ENDPOINT_NOTIFICATIONS);
    }

    public function getNotification($notification_id, $app_id = null) {
        $this->requiresAuth();
        $this->usesJSON();

        if(!$app_id)
            $app_id = $this->appId;

        return $this->get(self::ENDPOINT_NOTIFICATIONS . '/'.$notification_id . '?app_id='.$app_id);
    }

    public function getNotifications($app_id = null, $limit = null, $offset = null) {
        $this->requiresAuth();
        $this->usesJSON();

        $endpoint = self::ENDPOINT_NOTIFICATIONS;
        
        if(!$app_id) {
            $app_id = $this->appId;
        }

        $endpoint.='?app_id='.$app_id;

        if($limit) {
            $endpoint.="&limit=".$limit;
        }

        if($offset) {
            $endpoint.="&offset=".$$offset;
        }

        return $this->get($endpoint);
    }

    public function getApp($app_id = null) {
        $this->requiresUserAuth();
        $this->usesJSON();

        if(!$app_id)
            $app_id = $this->appId;

        return $this->get(self::ENDPOINT_APPS . '/'.$app_id);
    }

    public function getApps() {
        $this->requiresUserAuth();
        $this->usesJSON();

        return $this->get(self::ENDPOINT_APPS);
    }

    /**
     * Creates a user/player
     *
     * @param array $parameters
     * @return mixed
     * @throws \Exception
     */
    public function createPlayer(Array $parameters) {
        if(!isset($parameters['device_type']) or !is_numeric($parameters['device_type'])) {
            throw new \Exception('The `device_type` param is required as integer to create a player(device)');
        }
        return $this->sendPlayer($parameters, 'POST', self::ENDPOINT_PLAYERS);
    }

    /**
     * Edit a user/player
     *
     * @param array $parameters
     * @return mixed
     */
    public function editPlayer(Array $parameters) {
        return $this->sendPlayer($parameters, 'PUT', self::ENDPOINT_PLAYERS . '/' . $parameters['id']);
    }

    public function requestPlayersCSV($app_id = null, Array $parameters = null) {
        $this->requiresAuth();
        $this->usesJSON();

        $endpoint = self::ENDPOINT_PLAYERS."/csv_export?";
        $endpoint .= "app_id" . $app_id?$app_id:$this->appId;

        return $this->sendPlayer($parameters, 'POST', $endpoint);
    }

    /**
     * Create or update a by $method value
     *
     * @param array $parameters
     * @param $method
     * @param $endpoint
     * @return mixed
     */
    private function sendPlayer(Array $parameters, $method, $endpoint)
    {
        $this->requiresAuth();
        $this->usesJSON();

        $parameters['app_id'] = $this->appId;
        $this->headers['body'] = json_encode($parameters);

        $method = strtolower($method);
        return $this->{$method}($endpoint);
    }

    public function post($endPoint) {
        if($this->requestAsync === true) {
            $promise = $this->client->postAsync(self::API_URL . $endPoint, $this->headers);
            return (is_callable($this->requestCallback) ? $promise->then($this->requestCallback) : $promise);
        }
        return $this->client->post(self::API_URL . $endPoint, $this->headers);
    }

    public function put($endPoint) {
        if($this->requestAsync === true) {
            $promise = $this->client->putAsync(self::API_URL . $endPoint, $this->headers);
            return (is_callable($this->requestCallback) ? $promise->then($this->requestCallback) : $promise);
        }
        return $this->client->put(self::API_URL . $endPoint, $this->headers);
    }

    public function get($endPoint) {
        return $this->client->get(self::API_URL . $endPoint, $this->headers);
    }

    public function delete($endPoint) {
        if($this->requestAsync === true) {
            $promise = $this->client->deleteAsync(self::API_URL . $endPoint, $this->headers);
            return (is_callable($this->requestCallback) ? $promise->then($this->requestCallback) : $promise);
        }
        return $this->client->delete(self::API_URL . $endPoint, $this->headers);
    }
}
