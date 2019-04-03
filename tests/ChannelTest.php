<?php

namespace NotificationChannels\WebPush\Test;

use Illuminate\Contracts\Events\Dispatcher;
use Minishlink\WebPush\MessageSentReport;
use Minishlink\WebPush\WebPush;
use Mockery;
use NotificationChannels\WebPush\WebPushChannel;

class ChannelTest extends TestCase
{
    /** @var Mockery\Mock */
    protected $webPush;

    /** @var \NotificationChannels\WebPush\WebPushChannel */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();

        $this->webPush = Mockery::mock(WebPush::class);

        $this->channel = new WebPushChannel($this->app->make(Dispatcher::class), $this->webPush);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     * @throws \Exception
     * @todo fix test since updated push library uses a "subscription" object and this gives issues during mocking
     * @todo fix flush test since updated push library uses a generated which returns a MessageSentReport object we need to mock here
     */
    public function it_can_send_a_notification()
    {
        $this->webPush->shouldReceive('sendNotification')
            ->once()
            ->andReturn(true);

        $this->webPush->shouldReceive('flush')
            ->once()
            ->andReturnUsing(function () {
                yield from [];
            });

        $this->testUser->updatePushSubscription('endpoint', 'key', 'token');

        $this->channel->send($this->testUser, new TestNotification);

        $this->assertTrue(true);
    }

    /**
     * @test
     * @throws \Exception
     * @todo fix sendNotification test since updated push library uses a "subscription" object and this gives issues during mocking
     * @todo fix flush test since updated push library uses a generated which returns a MessageSentReport object we need to mock here
     */
    public function it_will_delete_invalid_subscriptions()
    {
        $this->webPush->shouldReceive('sendNotification')
            ->twice()
            ->andReturn(true);

        $this->webPush->shouldReceive('flush')
            ->once()
            ->andReturnUsing(function () {
                yield from [];
            });

        $this->testUser->updatePushSubscription('valid_endpoint');
        $this->testUser->updatePushSubscription('invalid_endpoint');

        $this->channel->send($this->testUser, new TestNotification);

        $this->assertFalse($this->testUser->webPushSubscriptions()->where('endpoint', 'invalid_endpoint')->exists());

        $this->assertTrue($this->testUser->webPushSubscriptions()->where('endpoint', 'valid_endpoint')->exists());
    }

    /**
     * @return string
     */
    protected function getPayload()
    {
        return json_encode([
            'title' => 'Title',
            'actions' => [
                ['title' => 'Title', 'action' => 'Action'],
            ],
            'body' => 'Body',
            'icon' => 'Icon',
            'data' => ['id' => 1],
        ]);
    }
}
