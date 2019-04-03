<?php

namespace NotificationChannels\WebPush\Test;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\File;
use NotificationChannels\WebPush\WebPushServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /** @var \NotificationChannels\WebPush\Test\User */
    protected $testUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);

        $this->testUser = User::first();
    }

    /**
     * Set up the environment.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $this->initializeDirectory($this->getTempDirectory());

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => $this->getTempDirectory() . '/database.sqlite',
            'prefix' => '',
        ]);

        $app['config']->set('auth.providers.users.model', User::class);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            WebPushServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        file_put_contents($this->getTempDirectory() . '/database.sqlite', null);

        $app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
        });

        include_once __DIR__ . '/../migrations/create_push_subscriptions_table.php.stub';

        (new \CreateWebPushSubscriptionsTable())->up();

        $this->createUser(['email' => 'test@user.com']);
    }

    /**
     * Initialize the directory.
     *
     * @param string $directory
     */
    protected function initializeDirectory($directory)
    {
        if (File::isDirectory($directory)) {
            File::deleteDirectory($directory);
        }
        if (!File::exists($directory)) {
            File::makeDirectory($directory);
        }
    }

    /**
     * @param string $suffix
     * @return string
     */
    public function getTempDirectory($suffix = '')
    {
        return __DIR__ . '/temp' . ($suffix == '' ? '' : '/' . $suffix);
    }

    /**
     * @param array $attributes
     * @return \NotificationChannels\WebPush\Test\User
     */
    public function createUser(array $attributes)
    {
        return User::create($attributes);
    }

    /**
     * @param \NotificationChannels\WebPush\Test\User $user
     * @param string $endpoint
     * @return \NotificationChannels\WebPush\WebPushSubscription
     */
    public function createSubscription(User $user, string $endpoint = 'endpoint')
    {
        /** @var \NotificationChannels\WebPush\WebPushSubscription $subscription */
        $subscription = $user->webPushSubscriptions()
            ->create([
                'endpoint' => $endpoint,
                'public_key' => 'key',
                'auth_token' => 'token',
            ]);

        return $subscription;
    }

    /**
     * @param string $expectedText
     */
    protected function seeInConsoleOutput(string $expectedText)
    {
        $consoleOutput = $this->app[Kernel::class]->output();

        $this->assertContains($expectedText, $consoleOutput, "Did not see `{$expectedText}` in console output: `$consoleOutput`");
    }
}
