<?php

namespace NotificationChannels\WebPush\Test;

class HasPushSubscriptionsTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_get_user_subscriptions()
    {
        $this->createSubscription($this->testUser, 'foo');
        $this->createSubscription($this->testUser, 'bar');

        $this->assertTrue($this->testUser->webPushSubscriptions()->where('endpoint', 'foo')->exists());

        $this->assertTrue($this->testUser->webPushSubscriptions()->where('endpoint', 'bar')->exists());

        $this->assertEquals(2, count($this->testUser->routeNotificationForWebPush()));
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_create_a_new_subscription()
    {
        $this->testUser->updatePushSubscription('foo', 'key', 'token');
        $sub = $this->testUser->webPushSubscriptions()->first();

        $this->assertEquals('foo', $sub->endpoint);

        $this->assertEquals('key', $sub->public_key);

        $this->assertEquals('token', $sub->auth_token);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_update_an_existing_subscription_by_endpoint()
    {
        $this->testUser->updatePushSubscription('foo', 'key', 'token');
        $this->testUser->updatePushSubscription('foo', 'major-key', 'another-token');
        $subs = $this->testUser->webPushSubscriptions()->where('endpoint', 'foo')->get();

        $this->assertEquals(1, count($subs));

        $this->assertEquals('major-key', $subs[0]->public_key);

        $this->assertEquals('another-token', $subs[0]->auth_token);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_determine_if_a_subscription_belongs_to_a_user()
    {
        $sub = $this->testUser->updatePushSubscription('foo');

        $this->assertTrue($this->testUser->pushSubscriptionBelongsToUser($sub));
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_will_delete_a_subscription_that_belongs_to_another_user_and_save_it_for_the_new_user()
    {
        $otherUser = $this->createUser(['email' => 'other@user.com']);
        $otherUser->updatePushSubscription('foo');
        $this->testUser->updatePushSubscription('foo');

        $this->assertEquals(0, count($otherUser->webPushSubscriptions));

        $this->assertEquals(1, count($this->testUser->webPushSubscriptions));
    }

    /**
     * @throws \Exception
     */
    public function it_will_delete_a_subscription_by_endpoint()
    {
        $this->testUser->updatePushSubscription('foo');
        $this->testUser->deletePushSubscription('foo');

        $this->assertEquals(0, count($this->testUser->webPushSubscriptions));
    }
}
