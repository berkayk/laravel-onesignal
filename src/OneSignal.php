<?php

namespace Berkayk\OneSignal;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Berkayk\OneSignal\OneSignalClient addParams($params = [])
 * @method static \Berkayk\OneSignal\OneSignalClient setParam($key, $value)
 * @method static \Berkayk\OneSignal\OneSignalClient sendNotificationToExternalUser($message, $userId, $url = null, $data = null, $buttons = null, $schedule = null, $headings = null, $subtitle = null)
 * @method static \Berkayk\OneSignal\OneSignalClient sendNotificationUsingTags($message, $tags, $url = null, $data = null, $buttons = null, $schedule = null, $headings = null, $subtitle = null)
 * @method static \Berkayk\OneSignal\OneSignalClient sendNotificationToAll($message, $url = null, $data = null, $buttons = null, $schedule = null, $headings = null, $subtitle = null)
 * @method static \Berkayk\OneSignal\OneSignalClient sendNotificationToSegment($message, $segment, $url = null, $data = null, $buttons = null, $schedule = null, $headings = null, $subtitle = null)
 * @method static \Berkayk\OneSignal\OneSignalClient deleteNotification($notificationId, $appId = null)
 * @method static \Berkayk\OneSignal\OneSignalClient sendNotificationCustom($parameters = [])
 * @method static \Berkayk\OneSignal\OneSignalClient getNotification($notification_id, $app_id = null)
 * @method static \Berkayk\OneSignal\OneSignalClient getNotifications($app_id = null, $limit = null, $offset = null)
 * @method static \Berkayk\OneSignal\OneSignalClient getApp($app_id = null)
 * @method static \Berkayk\OneSignal\OneSignalClient getApps()
 * @method static \Berkayk\OneSignal\OneSignalClient createPlayer(Array $parameters)
 * @method static \Berkayk\OneSignal\OneSignalClient editPlayer(Array $parameters)
 * @method static \Berkayk\OneSignal\OneSignalClient requestPlayersCSV($app_id = null, Array $parameters = null)
 * @method static \Berkayk\OneSignal\OneSignalClient post($endPoint)
 * @method static \Berkayk\OneSignal\OneSignalClient put($endPoint)
 * @method static \Berkayk\OneSignal\OneSignalClient get($endPoint)
 * @method static \Berkayk\OneSignal\OneSignalClient delete($endPoint)
 * @method static \Berkayk\OneSignal\OneSignalClient sendNotificationToUser($message, $userId, $url = null, $data = null, $buttons = null, $schedule = null, $headings = null, $subtitle = null)
 *
 * @see \Illuminate\Http\Client\Factory
 */
class OneSignal extends Facade {
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'onesignal';
    }
}
