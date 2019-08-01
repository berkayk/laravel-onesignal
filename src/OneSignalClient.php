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
    protected $additionalParams;
    protected $appsConfig;
    protected $currentAppConfig;
    protected $currentParams;

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

    public function __construct(array $appsConfig)
    {
        $this->appsConfig = $appsConfig;

        $this->client = new Client([
            'handler' => $this->createGuzzleHandler(),
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
        return "APP ID: ".$this->currentAppConfig['app_id']." REST: ".$this->currentAppConfig['rest_api_key'];
    }

    private function requiresAuth() {
        $this->headers['headers']['Authorization'] = 'Basic '.$this->currentAppConfig['rest_api_key'];
    }

    private function requiresUserAuth() {
        $this->headers['headers']['Authorization'] = 'Basic '.$this->currentAppConfig['user_auth_key'];
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

    public function app($appName){
        $this->currentAppConfig = $this->appsConfig[$appName];
        $this->currentParams['app_id'] = $this->appsConfig[$appName]['app_id'];
        return $this;
    }

    public function channelId($channelId){
        $this->currentParams['android_channel_id'] = $channelId;
        return $this;
    }

    public function getChannelId(){
        return $this->currentParams['android_channel_id'];
    }

    public function message($message){
        $this->currentParams['contents'] = array(
            "en" => $message
        );
        return $this;
    }

    public function getMessage($message){
        return $this->currentParams['contents']['en'];
    }

    public function url($url){
        $this->currentParams['url'] = $url;
        return $this;
    }

    public function getUrl(){
        $this->currentParams['url'];
    }

    public function data($data){
        $this->currentParams['data'] = $data;
        return $this;
    }

    public function getData(){
        return $this->currentParams['data'];
    }

    public function buttons($buttons){
        $this->currentParams['buttons'] = $buttons;
        return $this;
    }

    public function getButtons(){
        return $this->currentParams['buttons'];
    }
    
    public function schedule($schedule){
        $this->currentParams['send_after'] = $schedule;
        return $this;
    }

    public function getSchedule(){
        return $this->currentParams['send_after'];
    }

    public function headings($headings){
        $this->currentParams['headings'] = array(
            "en" => $headings
        );
        return $this;
    }

    public function getHeadings(){
        return $this->currentParams['headings']['en'];
    }

    public function subtitle($subtitle){
        $this->currentParams['subtitle'] = array(
            "en" => $subtitle
        );
        return $this;
    }

    public function getSubtitle(){
        return $this->currentParams['subtitle']['en'];
    }

    public function accentColor($accentColor){
        $this->currentParams['android_accent_color'] = $accentColor;
        return $this;
    }

    public function getAccentColor(){
        return $this->currentParams['android_accent_color'];
    }

    public function largeIcon($largeIcon){
        $this->currentParams['large_icon'] = $largeIcon;
        return $this;
    }

    public function getLargeIcon(){
        return $this->currentParams['large_icon'];
    }

    public function includedSegments($includedSegments){
        $this->currentParams['included_segments'] = $includedSegments;
        return $this;
    }

    public function getIncludedSegments(){
        return isset($this->currentParams['included_segments']) ? $this->currentParams['included_segments'] : null;
    }

    public function includePlayerIds($includePlayerIds){
        $this->currentParams['include_player_ids'] = s_array($includePlayerIds) ? $includePlayerIds : array($includePlayerIds);
        return $this;        
    }

    public function getIncludePlayerIds(){
        return isset($this->currentParams['include_player_ids']) ? $this->currentParams['include_player_ids'] : null;
    }

    public function filters($filters){
        $this->currentParams['filters'] = $filters;
        return $this;
    }

    public function getFilters(){
        return isset($this->currentParams['filters']) ? $this->currentParams['filters'] : null;
    }

    public function hasNeedAddIncludedSegment(){
        return $this->getIncludePlayerIds() == null && $this->getIncludedSegments() == null && $this->getFilters() == null;
    }

    public function send(){
        if ($this->hasNeedAddIncludedSegment()){
            $this->includedSegments(array('All'));
        }
        $this->sendNotificationCustom($this->currentParams);
        return $this;
    }

    public function deleteNotification($notificationId, $appId = null) {
        $this->requiresAuth();

        if(!$appId)
            $appId = $this->currentAppConfig['app_id'];
        $notificationCancelNode = "/$notificationId?app_id=$this->currentAppConfig['app_id']";
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
            $parameters['app_id'] = $this->currentAppConfig['app_id'];
        }

        // Make sure to use included_segments
        if (empty($parameters['included_segments']) && empty($parameters['include_player_ids'])) {
            $parameters['included_segments'] = ['all'];
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
            $app_id = $this->currentAppConfig['app_id'];

        return $this->get(self::ENDPOINT_NOTIFICATIONS . '/'.$notification_id . '?app_id='.$app_id);
    }

    public function getNotifications($app_id = null, $limit = null, $offset = null) {
        $this->requiresAuth();
        $this->usesJSON();

        $endpoint = self::ENDPOINT_NOTIFICATIONS;
        
        if(!$app_id) {
            $app_id = $this->currentAppConfig['app_id'];
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
            $app_id = $this->currentAppConfig['app_id'];

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
        $endpoint .= "app_id" . $app_id?$app_id:$this->currentAppConfig['app_id'];

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

        $parameters['app_id'] = $this->currentAppConfig['app_id'];
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
