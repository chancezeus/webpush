<?php

namespace NotificationChannels\WebPush;

/**
 * @link https://developer.mozilla.org/en-US/docs/Web/API/ServiceWorkerRegistration/showNotification#Parameters
 */
class WebPushMessage
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var array
     */
    protected $actions = [];

    /**
     * @var string|null
     */
    protected $badge;

    /**
     * @var string|null
     */
    protected $body;

    /**
     * @var string|null
     */
    protected $dir = 'auto';

    /**
     * @var string|null
     */
    protected $icon;

    /**
     * @var string|null
     */
    protected $image;

    /**
     * @var string|null
     */
    protected $lang;

    /**
     * @var bool
     */
    protected $renotify = false;

    /**
     * @var bool
     */
    protected $requireInteraction = false;

    /**
     * @var string|null
     */
    protected $tag;

    /**
     * @var array|null
     */
    protected $vibrate;

    /**
     * @var mixed|null
     */
    protected $data;

    /**
     * Set the notification title.
     *
     * @param string $value
     * @return $this
     */
    public function title(string $value): WebPushMessage
    {
        $this->title = $value;

        return $this;
    }

    /**
     * Add a notification action.
     *
     * @param string $title
     * @param string $action
     * @param string|null $icon
     * @return $this
     */
    public function action(string $title, string $action, string $icon = null): WebPushMessage
    {
        $action = compact('title', 'action');
        if ($icon) {
            $action['icon'] = $icon;
        }

        $this->actions[] = $action;

        return $this;
    }

    /**
     * Set the notification badge.
     *
     * @param string|null $value
     * @return $this
     */
    public function badge(?string $value): WebPushMessage
    {
        $this->badge = $value;

        return $this;
    }

    /**
     * Set the notification body.
     *
     * @param string|null $value
     * @return $this
     */
    public function body(?string $value): WebPushMessage
    {
        $this->body = $value;

        return $this;
    }

    /**
     * Set the notification direction.
     *
     * @param string $value
     * @return $this
     */
    public function dir(string $value = 'auto'): WebPushMessage
    {
        $this->dir = $value;

        return $this;
    }

    /**
     * Set the notification icon url.
     *
     * @param string|null $value
     * @return $this
     */
    public function icon(?string $value): WebPushMessage
    {
        $this->icon = $value;

        return $this;
    }

    /**
     * Set the notification image url.
     *
     * @param string|null $value
     * @return $this
     */
    public function image(?string $value): WebPushMessage
    {
        $this->image = $value;

        return $this;
    }

    /**
     * Set the notification language.
     *
     * @param string|null $value
     * @return $this
     */
    public function lang(?string $value): WebPushMessage
    {
        $this->lang = $value;

        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function renotify(bool $value = true): WebPushMessage
    {
        $this->renotify = $value;

        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function requireInteraction(bool $value = true): WebPushMessage
    {
        $this->requireInteraction = $value;

        return $this;
    }

    /**
     * Set the notification tag.
     *
     * @param string|null $value
     * @return $this
     */
    public function tag(?string $value): WebPushMessage
    {
        $this->tag = $value;

        return $this;
    }

    /**
     * Set the notification vibration pattern.
     *
     * @param array $value
     * @return $this
     */
    public function vibrate(array $value): WebPushMessage
    {
        $this->vibrate = $value;

        return $this;
    }

    /**
     * Set the notification arbitrary data.
     *
     * @param mixed $value
     * @return $this
     */
    public function data($value): WebPushMessage
    {
        $this->data = $value;

        return $this;
    }

    /**
     * Get an array representation of the message.
     *
     * @return array
     */
    public function toArray()
    {
        return array_filter(get_object_vars($this), function ($value) {
            return null !== $value;
        });
    }
}
