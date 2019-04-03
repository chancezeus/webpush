<?php

namespace NotificationChannels\WebPush;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $endpoint
 * @property string $public_key
 * @property string $auth_token
 * @property-read \Illuminate\Database\Eloquent\Model $user
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class WebPushSubscription extends Model implements Contracts\WebPushSubscription
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'endpoint',
        'public_key',
        'auth_token',
    ];

    /**
     * Get the user that owns the subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function user()
    {
        return $this->morphTo('user', 'user_type', 'user_id');
    }

    /**
     * Find a subscription by the given endpoint.
     *
     * @param string $endpoint
     * @return static|null
     */
    public static function findByEndpoint($endpoint)
    {
        return static::where('endpoint', $endpoint)->first();
    }

    /**
     * Return the subscriptions endpoint
     *
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Return the subscriptions public key
     *
     * @return string|null
     */
    public function getPublicKey(): ?string
    {
        return $this->public_key;
    }

    /**
     * Return the subscriptions auth token
     *
     * @return string|null
     */
    public function getAuthToken(): ?string
    {
        return $this->auth_token;
    }
}
