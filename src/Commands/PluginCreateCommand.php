<?php

namespace Tec\PluginManagement\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Tec\Base\Http\Controllers\BaseController;
use Tec\Base\Http\Responses\BaseHttpResponse;
use Tec\Base\Traits\HasDeleteManyItemsTrait;
use Tec\Base\Traits\LoadAndPublishDataTrait;
use Tec\PluginManagement\Abstracts\PluginOperationAbstract;
use Tec\PluginManagement\Services\PluginService;
use Tec\Skroutz\Commands\GenerateSkrutzXMLCommand;
use Tec\Skroutz\Http\Controllers\SkroutzController;
use Tec\Skroutz\Models\SkroutzModel;
use Tec\Skroutz\Providers\SkroutzCommandServiceProvider;

class PluginCreateCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'cms:plugin:create  {name : The plugin that you want to create}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create New plugin in /plugins directory';

    /**
     * @var PluginService
     */
    protected $pluginService;
    protected $pluginName;


    /**
     * PluginActivateCommand constructor.
     * @param PluginService $pluginService
     */
    public function __construct(PluginService $pluginService)
    {
        parent::__construct();

        $this->pluginService = $pluginService;
    }

    /**
     * @return boolean
     * @throws FileNotFoundException
     */
    public function handle()
    {
        if (!preg_match('/^[a-z0-9\-]+$/i', $this->argument('name'))) {
            $this->error('Only alphabetic characters are allowed.');
            return 1;
        }
        $plugin = strtolower($this->argument('name'));
        if(File::exists(plugin_path($plugin ))){
            $this->error('This Plugin name Already exists.');
            return 1;
        }


        $this->pluginName=$plugin;
        $folders=[
            'folders'=>[
            'config'=>[
                'folders'=>[],
                'files'=>[
                'config'=>'general.php',
                'permissions'=>'permissions.php'
                    ]
            ],
            'database'=>[
                'folders'=>[],
                'files'=>[]
            ],
            'helpers'=>[
                'folders'=>[],
                'files'=>[]],
            'public'=>[
                'folders'=>['js'=>['folders'=>[],
                    'files'=>[]],'css'=>['folders'=>[],
                    'files'=>[]],'images'=>['folders'=>[],
                    'files'=>[]],
                    'files'=>[]],
            ],
            'resources'=>[
                'folders'=>[
                   'assets'=>[
                   'folders'=>[
                    'js'=>['folders'=>[],
                        'files'=>[]],
                    'css'=>['folders'=>[],
                        'files'=>[]],
                    'images'=>['folders'=>[],
                        'files'=>[]]
                    ],
                       'files'=>[]
                   ],
                   'lang'=>[
                        'folders'=>[
                        'en'=>[
                            'folders'=>[],
                            'files'=>[
                                'lang'=>$plugin.'.php',
                        ],

                    ]],
                       'files'=>[]
                          ],
                   'views'=>[
                        'folders'=>[],
                       'files'=>[
                           'index'=>'index.blade.php',
                       ]
                ],
                    'files'=>[]
                ],
                'files'=>[]
                ],
            'routes'=>[
                'folders'=>[],
                'files'=>[
                'route'=>'web.php',
                    ]
                ],
            'src'=>[
                'folders'=>[
                  'Commands'=>[
                      'folders'=>[],
                      'files'=>[]
                  ] ,
                  'Events'=>[
                      'folders'=>[],
                      'files'=>[]
                  ] ,
                  'Http' =>[
                      'folders'=>[
                          'Controllers'=>[
                              'folders'=>[],
                              'files'=>[
                              'controller'=>ucfirst($plugin).'Controller.php'
                                  ]
                          ]
                      ],
                      'files'=>[]
                  ] ,
                  'Listeners'=>[
                      'folders'=>[] ,
                      'files'=>[]
                  ]   ,
                  'Models' =>[
                      'folders'=>[],
                      'files'=>[]
                  ]  ,
                  'Providers'=>[
                      'folders'=>[],
                      'files'=>[
                      'provider'=>ucfirst($plugin).'ServiceProvider.php'
                          ]
                  ]  ,
                  'Repositories' =>[
                      'folders'=>[
                          'Caches'=>['folders'=>[],
                              'files'=>[]],
                          'Eloquent'=>['folders'=>[],
                              'files'=>[]],
                          'Interfaces'=>['folders'=>[],
                              'files'=>[]],
                          ],
                          'files'=>[]

                  ]  ,
                  'Tables'  =>[
                      'folders'=>[],
                      'files'=>[]
                  ] ,
                ],
                'files'=>[
                    'pluginfile'=> ucfirst($plugin).'.php',
                ],
                ],
             ],
            'files'=>[
            'json'=>'plugin.json',
            'plugin'=>'Plugin.php'
            ]
        ];
        //create main plugin Folder
        $this->createMainFolder($plugin,$folders);
        $this->info('Plugin Created successfully!');
        return 0;
    }

    /**
     * @Function   createMainFolder
     * @param $pluginName
     * @param $folders
     * @Author    : Michail Fragkiskos
     * @Created at: 14/11/2023 at 15:58
     * @param $pluginName
     * @param $folders
     */
    private function createMainFolder($pluginName,$folders){
        //create main plugin Folder
        File::ensureDirectoryExists(plugin_path($pluginName));
        foreach ($folders as $folder=>$subfolders){
            if($folder=='folders' && !empty($subfolders)){
                $this->createSubfolders($pluginName, $subfolders);
            }
            if($folder=='files' && !empty($subfolders)){
                //create files
              $this->createFiles($pluginName,$subfolders);
            }
        }

    }

    /**
     * @Function   createSubfolders
     * @param $pluginName
     * @param $subfolders
     * @Author    : Michail Fragkiskos
     * @Created at: 14/11/2023 at 15:58
     * @param $pluginName
     * @param $subfolders
     */
    private function createSubfolders($pluginName,$subfolders){

        foreach($subfolders as $_folder=>$_subfolders) {
            File::ensureDirectoryExists(plugin_path($pluginName . '/' . $_folder));
            if(isset($_subfolders['folders']) && !empty($_subfolders['folders'])){
                if (isset($_subfolders['folders']) && !empty($_subfolders['folders'])) {
                    $this->createSubfolders(($pluginName . '/' . $_folder), $_subfolders['folders']);
                }
            }
            if(isset($_subfolders['files']) && !empty($_subfolders['files'])){
                $this->createFiles(($pluginName . '/' . $_folder), $_subfolders['files']);
            }
        }
    }
    private function createFiles($pluginName,$files){

        foreach($files as $fileType=>$file) {
            $fileData='';
        switch(strtolower($fileType)){
            case 'json';
                $fileData= $this->jsonFile();
                break;
            case 'plugin';
                $fileData= $this->pluginFile();
                break;
            case 'route';
                $fileData= $this->routeFile();
                break;
                case 'index';
                $fileData= $this->indexFile();
                break;
                case 'lang';
                $fileData= $this->langFile();
                break;
            case 'provider';
                $fileData= $this->providerFile();
                break;
            case 'controller';
                $fileData= $this->controllerFile();
            break;
            case 'permissions';
                $fileData= $this->permissionsFile();
                break;
            case 'config';
                $fileData= $this->configFile();
            break;
        }

            File::ensureDirectoryExists(plugin_path($pluginName));
            file_put_contents(plugin_path($pluginName . '/' . str_replace([' ','-'], '',  $file)),$fileData);
        }
    }

    private function jsonFile(){
        return '{
    "name":"'.ucfirst($this->pluginName).' Plugin",
    "namespace": "Tec\\\\'.ucfirst(str_replace([' ','-'], '',  $this->pluginName)).'\\\\",
    "provider": "Tec\\\\'.ucfirst(str_replace([' ','-'], '',  $this->pluginName)).'\\\\Providers\\\\'.ucfirst(str_replace([' ','-'], '',  $this->pluginName)).'ServiceProvider",
    "author": "Tec Dynamics Technologies",
    "url": "https://tecdynamics.co.uk",
    "version": "1.0",
    "description":"'.$this->pluginName.' Plugin"
}';
    }
    private function pluginfileFile(){

    }
    private function indexFile(){
        $className=ucfirst($this->pluginName);
        $fname='Tec\\'.ucfirst($this->pluginName);
        return <<<"FILE_CONTENTS"
  $className
FILE_CONTENTS;

    }
    private function pluginFile(){
    $fname='Tec\\'.ucfirst(str_replace([' ','-'], '',  $this->pluginName));

        return <<<"FILE_CONTENTS"
<?php
 namespace $fname ;

  use Tec\PluginManagement\Abstracts\PluginOperationAbstract;
  class Plugin extends PluginOperationAbstract {}

FILE_CONTENTS;


    }
    private function routeFile(){
        $className=ucfirst($this->pluginName).'Controller';
        $fname='Tec\\'.ucfirst($this->pluginName);
  return <<<"FILE_CONTENTS"
<?php
        Route::group(['namespace' => '$fname\Http\Controllers', 'middleware' => ['web', 'core']], function () {

            Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

                Route::group(['prefix' => '$this->pluginName', 'as' => '$this->pluginName.'], function () {
                    Route::resource('', '$className');

                });
            });

        });
FILE_CONTENTS;
    }
    private function langFile(){
        $className=ucfirst($this->pluginName).'Controller';
        $fname= ucfirst($this->pluginName);
  return <<<"FILE_CONTENTS"
<?php

return [
    'name' => '$fname',
    'settings' => 'Settings',
      ];
FILE_CONTENTS;
    }
    private function providerFile(){
        $className=ucfirst(str_replace([' ','-'], '',  $this->pluginName)).'ServiceProvider';
        $fname='Tec\\'.ucfirst(str_replace([' ','-'], '',  $this->pluginName));
        $name=str_replace([' ','-'], '',  $this->pluginName);
        return <<<"FILE_CONTENTS"
<?php
  namespace $fname\\Providers;

    use Illuminate\Console\Scheduling\Schedule;
    use Illuminate\Routing\Events\RouteMatched;
    use Illuminate\Support\Facades\Event;
    use Illuminate\Support\ServiceProvider;
    use Tec\Base\Traits\LoadAndPublishDataTrait;

    class $className extends ServiceProvider {
    use LoadAndPublishDataTrait;

    public function boot() {

    \$this->setNamespace('plugins/$this->pluginName')
    ->loadAndPublishConfigurations(['general', 'permissions'])
    ->loadHelpers()->loadRoutes(['web'])
    ->loadAndPublishTranslations()
    ->loadAndPublishViews()
    ->loadMigrations()
    ->publishAssets();
        /*
       \$this->app->register(CommandServiceProvider::class);
            */
          Event::listen(RouteMatched::class, function () {
             dashboard_menu()
                ->registerItem([
                    'id' => 'cms-plugins-$this->pluginName',
                    'priority' => 5,
                    'parent_id' => null,
                    'name' => 'plugins/$this->pluginName::$name.name',
                    'icon' => 'fas fa-store',
                    'url' => '',
                    'permissions' => ['$this->pluginName.index'],
                ]);
             });

        \$this->app->booted(function () {
          /*
            add_action('product_deleted', [\$this, 'productdeleted'], 128, 1);
         \$this->app->make(Schedule::class)->command(GenerateCommand::class)->hourly();
            */
        });
        }
    }
FILE_CONTENTS;

 }

    private function controllerFile(){
        $className=ucfirst(str_replace([' ','-'], '',  $this->pluginName)).'Controller';
        $fname='Tec\\'.ucfirst(str_replace([' ','-'], '',  $this->pluginName));
        return <<<"FILE_CONTENTS"
<?php
namespace $fname\\Http\\Controllers;

use Assets;
use Illuminate\Http\Request;
use Language;
use Setting;
use Tec\Base\Http\Controllers\BaseController;
use Tec\Base\Http\Responses\BaseHttpResponse;
use Tec\Base\Traits\HasDeleteManyItemsTrait;

class $className extends BaseController {
    /**
     * $className constructor.
     */
    /*
     public function __construct() {

    }
    */

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(BaseHttpResponse \$response) {
    \$data=[];

        return view('plugins/$this->pluginName::index', \$data)->render();
    }
 }
FILE_CONTENTS;

  }
    private function permissionsFile(){
        $className=ucfirst(str_replace([' ','-'], '',  $this->pluginName));
        $fname='Tec\\'.ucfirst(str_replace([' ','-'], '',  $this->pluginName));
        return <<<"FILE_CONTENTS"
<?php
return [
    [
        'name' => '$className',
        'flag' => '$this->pluginName.index',
    ],
    [
        'name'        => 'Settings',
        'flag'        => '$this->pluginName.settings',
        'parent_flag' => '$this->pluginName.index',
    ],
];

FILE_CONTENTS;

    }
    private function configFile(){
        $className=ucfirst($this->pluginName);
        $fname='Tec\\'.ucfirst($this->pluginName);
        return <<<"FILE_CONTENTS"
<?php
return [];
FILE_CONTENTS;
    }




}
