<?php

namespace NotificationChannels\WebPush\Contracts;

interface WebPushSubscription
{
    /**
     * Return the subscriptions endpoint
     *
     * @return string
     */
    public function getEndpoint(): string;

    /**
     * Return the subscriptions public key
     *
     * @return string|null
     */
    public function getPublicKey(): ?string;

    /**
     * Return the subscriptions auth token
     *
     * @return string|null
     */
    public function getAuthToken(): ?string;
}
