<?php

namespace NotificationChannels\WebPush;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use NotificationChannels\WebPush\Contracts\WebPushSubscription;

class WebPushChannel
{
    /** @var \Illuminate\Contracts\Events\Dispatcher */
    protected $events;

    /** @var \Minishlink\WebPush\WebPush */
    protected $webPush;

    /**
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     * @param \Minishlink\WebPush\WebPush $webPush
     */
    public function __construct(Dispatcher $events, WebPush $webPush)
    {
        $this->events = $events;
        $this->webPush = $webPush;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return void
     * @throws \ErrorException
     * @throws \Exception
     */
    public function send($notifiable, Notification $notification)
    {
        $subscriptions = Collection::make($notifiable->routeNotificationFor('webPush'));

        if ($subscriptions->isEmpty() || !method_exists($notification, 'toWebPush')) {
            return;
        }

        $payload = json_encode($notification->toWebPush($notifiable, $notification)->toArray());

        $subscriptions->each(function (WebPushSubscription $subscription) use ($payload) {
            $this->webPush->sendNotification(
                new Subscription(
                    $subscription->getEndpoint(),
                    $subscription->getPublicKey(),
                    $subscription->getAuthToken()
                ),
                $payload
            );
        });

        $response = $this->webPush->flush();

        foreach ($response as $index => $value) {
            if (!$value->isSuccess()) {
                $this->events->dispatch(new NotificationFailed($notifiable, $notification, $this, [
                    'subscription' => $subscriptions[$index],
                    'expired' => $value->isSubscriptionExpired(),
                    'reason' => $value
                ]));
            }
        }
    }
}
