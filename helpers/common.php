<?php

use Tec\Base\Facades\BaseHelper;
use Tec\Setting\Facades\Setting;
use Illuminate\Support\Facades\File;

if (! function_exists('plugin_path')) {
    function plugin_path(string|null $path = null): string
    {
        return platform_path('plugins' . DIRECTORY_SEPARATOR . $path);
    }
}

if (! function_exists('is_plugin_active')) {
    function is_plugin_active(string $alias): bool
    {
        return in_array($alias, get_active_plugins());
    }
}

if (! function_exists('get_active_plugins')) {
    function get_active_plugins(): array
    {
        $activatedPlugins = Setting::get('activated_plugins');

        if (! $activatedPlugins) {
            return [];
        }

        $activatedPlugins = json_decode($activatedPlugins, true);

        if (! $activatedPlugins) {
            return [];
        }

        $plugins = array_unique($activatedPlugins);

        $existingPlugins = BaseHelper::scanFolder(plugin_path());

        return array_diff($plugins, array_diff($plugins, $existingPlugins));
    }
}

if (! function_exists('get_installed_plugins')) {
    function get_installed_plugins(): array
    {
        $list = [];
        $plugins = BaseHelper::scanFolder(plugin_path());

        if (! empty($plugins)) {
            foreach ($plugins as $plugin) {
                $path = plugin_path($plugin);
                if (! File::isDirectory($path) || ! File::exists($path . '/plugin.json')) {
                    continue;
                }

                $list[] = $plugin;
            }
        }

        return $list;
    }
}
