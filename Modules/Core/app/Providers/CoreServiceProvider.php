<?php

namespace Modules\Core\Providers;

use Modules\Core\Services\ApiPayloadEncrypter;
use Modules\Core\Services\Sms\GatewayApiSmsDriver;
use Modules\Core\Services\Sms\SmsGatewayInterface;
use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class CoreServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Core';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'core';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    // protected array $commands = [];

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function register(): void
    {
        parent::register();

        $this->app->bind(SmsGatewayInterface::class, GatewayApiSmsDriver::class);

        $this->app->singleton(ApiPayloadEncrypter::class, fn ($app) => new ApiPayloadEncrypter(
            $app['config']->get('api_encryption.key'),
            $app['config']->get('api_encryption.except_keys', []),
        ));
    }

    /**
     * Define module schedules.
     * 
     * @param $schedule
     */
    // protected function configureSchedules(Schedule $schedule): void
    // {
    //     $schedule->command('inspire')->hourly();
    // }
}
