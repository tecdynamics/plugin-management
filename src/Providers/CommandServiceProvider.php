<?php

namespace Tec\PluginManagement\Providers;

use Tec\Base\Supports\ServiceProvider;
use Tec\PluginManagement\Commands\ClearCompiledCommand;
use Tec\PluginManagement\Commands\IlluminateClearCompiledCommand as OverrideIlluminateClearCompiledCommand;
use Tec\PluginManagement\Commands\PackageDiscoverCommand;
use Tec\PluginManagement\Commands\PluginActivateAllCommand;
use Tec\PluginManagement\Commands\PluginActivateCommand;
use Tec\PluginManagement\Commands\PluginAssetsPublishCommand;
use Tec\PluginManagement\Commands\PluginDeactivateAllCommand;
use Tec\PluginManagement\Commands\PluginDeactivateCommand;
use Tec\PluginManagement\Commands\PluginDiscoverCommand;
use Tec\PluginManagement\Commands\PluginInstallFromMarketplaceCommand;
use Tec\PluginManagement\Commands\PluginListCommand;
use Tec\PluginManagement\Commands\PluginRemoveAllCommand;
use Tec\PluginManagement\Commands\PluginRemoveCommand;
use Tec\PluginManagement\Commands\PluginUpdateVersionInfoCommand;
use Illuminate\Foundation\Console\ClearCompiledCommand as IlluminateClearCompiledCommand;
use Illuminate\Foundation\Console\PackageDiscoverCommand as IlluminatePackageDiscoverCommand;

class CommandServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->extend(IlluminatePackageDiscoverCommand::class, function () {
            return $this->app->make(PackageDiscoverCommand::class);
        });

        $this->app->extend(IlluminateClearCompiledCommand::class, function () {
            return $this->app->make(OverrideIlluminateClearCompiledCommand::class);
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                PluginAssetsPublishCommand::class,
                ClearCompiledCommand::class,
                PluginDiscoverCommand::class,
                PluginInstallFromMarketplaceCommand::class,
                PluginActivateCommand::class,
                PluginActivateAllCommand::class,
                PluginDeactivateCommand::class,
                PluginDeactivateAllCommand::class,
                PluginRemoveCommand::class,
                PluginRemoveAllCommand::class,
                PluginListCommand::class,
                PluginUpdateVersionInfoCommand::class,
            ]);
        }
    }
}
