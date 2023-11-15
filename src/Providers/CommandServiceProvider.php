<?php

namespace Tec\PluginManagement\Providers;

use Tec\PluginManagement\Commands\PluginActivateAllCommand;
use Tec\PluginManagement\Commands\PluginActivateCommand;
use Tec\PluginManagement\Commands\PluginAssetsPublishCommand;
use Tec\PluginManagement\Commands\PluginCreateCommand;
use Tec\PluginManagement\Commands\PluginDeactivateAllCommand;
use Tec\PluginManagement\Commands\PluginDeactivateCommand;
use Tec\PluginManagement\Commands\PluginRemoveCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                PluginAssetsPublishCommand::class,
            ]);
        }

        $this->commands([
            PluginActivateCommand::class,
            PluginDeactivateCommand::class,
            PluginRemoveCommand::class,
            PluginActivateAllCommand::class,
            PluginDeactivateAllCommand::class,
            PluginCreateCommand::class
        ]);
    }
}
