<?php

namespace Tec\PluginManagement\Providers;

use Tec\Base\Events\SeederPrepared;
use Tec\Base\Events\SystemUpdateDBMigrated;
use Tec\Base\Events\SystemUpdatePublished;
use Tec\Installer\Events\InstallerFinished;
use Tec\PluginManagement\Listeners\ActivateAllPlugins;
use Tec\PluginManagement\Listeners\ClearPluginCaches;
use Tec\PluginManagement\Listeners\CoreUpdatePluginsDB;
use Tec\PluginManagement\Listeners\PublishPluginAssets;
use Illuminate\Contracts\Database\Events\MigrationEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        MigrationEvent::class => [
            ClearPluginCaches::class,
        ],
        InstallerFinished::class => [
            ClearPluginCaches::class,
        ],
        SystemUpdateDBMigrated::class => [
            CoreUpdatePluginsDB::class,
        ],
        SystemUpdatePublished::class => [
            PublishPluginAssets::class,
        ],
        SeederPrepared::class => [
            ActivateAllPlugins::class,
        ],
    ];
}
