<?php

use Tec\Base\Facades\AdminHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Tec\PluginManagement\Http\Controllers'], function () {
    AdminHelper::registerRoutes(function () {
        Route::group(['prefix' => 'plugins'], function () {

            if (config('packages.plugin-management.general.enable_plugin_manager', true)) {
                Route::redirect('', 'plugins/installed');
                Route::get('installed', [
                    'as' => 'plugins.index',
                    'uses' => 'PluginManagementController@index',
                ]);

                Route::put('status', [
                    'as' => 'plugins.change.status',
                    'uses' => 'PluginManagementController@update',
                    'permission' => 'plugins.index',
                ]);

                Route::delete('{plugin}', [
                    'as' => 'plugins.remove',
                    'uses' => 'PluginManagementController@destroy',
                  'permission' => 'plugins.index',
                ]);

                Route::post('check-requirement', [
                    'as' => 'plugins.check-requirement',
                    'uses' => 'PluginManagementController@checkRequirement',
                    'permission' => 'plugins.index',
                ]);
            }

            if (config('packages.plugin-management.general.enable_marketplace_feature', true)) {
                Route::get('new', [
                    'as' => 'plugins.new',
                    'uses' => 'MarketplaceController@index',
                    'permission' => 'plugins.marketplace',
                ]);

                Route::group([
                    'prefix' => 'marketplace/ajax',
                    'permission' => 'plugins.marketplace',
                    'as' => 'plugins.marketplace.ajax.',
                ], function () {
                    Route::get('plugins', [
                        'as' => 'list',
                        'uses' => 'MarketplaceController@list',
                    ]);

                    Route::get('{id}', [
                        'as' => 'detail',
                        'uses' => 'MarketplaceController@detail',
                    ]);

                    Route::get('{id}/iframe', [
                        'as' => 'iframe',
                        'uses' => 'MarketplaceController@iframe',
                    ]);

                    Route::post('{id}/install', [
                        'as' => 'install',
                        'uses' => 'MarketplaceController@install',
               ]);

                    Route::post('{id}/update/{name?}', [
                        'as' => 'update',
                        'uses' => 'MarketplaceController@update',
                    ]);

                    Route::post('check-update', [
                        'as' => 'check-update',
                        'uses' => 'MarketplaceController@checkUpdate',
                    ]);
                });
            }
        });
    });
});
