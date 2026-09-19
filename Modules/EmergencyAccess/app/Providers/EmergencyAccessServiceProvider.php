<?php

namespace Modules\EmergencyAccess\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Modules\EmergencyAccess\Console\Commands\ExpireEmergencyAccessRequests;

class EmergencyAccessServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'EmergencyAccess';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'emergencyaccess';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    protected array $commands = [
        ExpireEmergencyAccessRequests::class,
    ];

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    /**
     * Define module schedules.
     *
     * @param $schedule
     */
    protected function configureSchedules(Schedule $schedule): void
    {
        $schedule->command(ExpireEmergencyAccessRequests::class)->everyFiveMinutes();
    }
}
