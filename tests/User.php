<?php

namespace NotificationChannels\WebPush\Test;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;

/**
 * @property int $id
 * @property string $email
 * @property-read \Illuminate\Database\Eloquent\Collection|\NotificationChannels\WebPush\WebPushSubscription $webPushSubscriptions
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class User extends Authenticatable
{
    use HasPushSubscriptions;
    use Notifiable;

    public $timestamps = false;

    protected $fillable = ['email'];
}
