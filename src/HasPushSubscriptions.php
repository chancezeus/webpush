<?php

namespace NotificationChannels\WebPush;

use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\NotificationChannels\WebPush\WebPushSubscription[] $webPushSubscriptions
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasPushSubscriptions
{
    /**
     * Get the user's subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany|\NotificationChannels\WebPush\WebPushSubscription
     */
    public function webPushSubscriptions(): MorphMany
    {
        return $this->morphMany(
            WebPushSubscription::class,
            'user',
            'user_type',
            'user_id',
            $this->getKeyName()
        );
    }

    /**
     * Update (or create) user subscription.
     *
     * @param string $endpoint
     * @param string|null $key
     * @param string|null $token
     * @return \NotificationChannels\WebPush\WebPushSubscription
     * @throws \Exception
     */
    public function updatePushSubscription(string $endpoint, string $key = null, string $token = null)
    {
        /** @var \NotificationChannels\WebPush\WebPushSubscription $subscription */
        $subscription = WebPushSubscription::query()
            ->where('endpoint', $endpoint)
            ->firstOrNew([
                'endpoint' => $endpoint
            ]);

        $subscription->user()->associate($this);
        $subscription->public_key = $key;
        $subscription->auth_token = $token;
        $subscription->save();

        return $subscription;
    }

    /**
     * Determine if the given subscription belongs to this user.
     *
     * @param \NotificationChannels\WebPush\WebPushSubscription $subscription
     * @return bool
     */
    public function pushSubscriptionBelongsToUser(WebPushSubscription $subscription)
    {
        /** @var \Illuminate\Database\Eloquent\Model $user */
        $user = $subscription->user;

        return $user->is($this);
    }

    /**
     * Delete subscription by endpoint.
     *
     * @param string $endpoint
     * @return void
     */
    public function deletePushSubscription(string $endpoint)
    {
        $this->webPushSubscriptions()
            ->where('endpoint', $endpoint)
            ->delete();
    }

    /**
     * Get all subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function routeNotificationForWebPush()
    {
        return $this->webPushSubscriptions;
    }
}
