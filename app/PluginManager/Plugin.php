<?php
namespace App\PluginManager;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

abstract class Plugin
{
    protected $app;

    /**
     * The Plugin Name.
     *
     * @var string
     */
    public $name;

    /**
     * A description of the plugin.
     * 
     * @var string
     */
    public $description;

    /**
     * The version of the plugin.
     * 
     * @var string
     */
    public $version;

    /**
     * The author of the plugin.
     * 
     * @var string
     */
    public $author;

    /**
     * Domain of the plugin.
     * 
     * @var string
     */
    public $domain;

    /**
     * Domain of the plugin.
     * 
     * @var string
     */
    public $purchase_code;

        /**
     * Author Url of the plugin.
     * 
     * @var string
     */
    public $author_url;

    /**
     * Tags of the plugin.
     * 
     * @var string
     */
    public $tags;

    /**
     * Unique Plugin Identifier.
     * 
     * @var string
     */
    public $plugin_identifier;

    /**
     * Minimum required Version of the CMS.
     * 
     * @var string
     */
    public $required_cms_version;

    /**
     * License Name.
     * 
     * @var string
     */
    public $license;

    /**
     * License URL.
     * 
     * @var string
     */
    public $license_url;

    /**
     * @var $this
     */
    private $reflector = null;

    /**
     * Plugin constructor.
     *
     * @param $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        $this->checkPluginName();
        $this->migratePlugin();
    }

    abstract public function boot();

    /**
     * Plugin Activate.
     *
     */
    public function pluginActivated(){

    }

    /**
     * Plugin Deactivate.
     *
     */
    public function pluginDeactivated(){

    }

    /**
     * Check for empty plugin name.
     *
     * @throws \InvalidArgumentException
     */
    private function checkPluginName()
    {
        if (!$this->name) {
            throw new \InvalidArgumentException('Missing Plugin name.');
        }
    }

    /**
     * Returns the view namespace in a camel case format based off
     * the plugins class name, with plugin stripped off the end.
     * 
     * Eg: ArticlesPlugin will be accessible through 'plugin:articles::<view name>'
     *
     * @return string
     */
    protected function getViewNamespace()
    {
        return 'plugin:' . Str::camel(
            mb_substr(
                get_called_class(),
                strrpos(get_called_class(), '\\') + 1,
                -6
            )
        );
    }

    /**
     * Add a view namespace for this plugin.
     * Eg: view("plugin:articles::{view_name}")
     *
     * @param string $path
     */
    protected function enableViews($path = 'views')
    {
        $this->app['view']->addNamespace(
            $this->getViewNamespace(),
            $this->getPluginPath() . DIRECTORY_SEPARATOR . $path
        );
    }

    /**
     * Enable routes for this plugin.
     *
     * @param string $path
     * @param array|string $middleware
     */
    protected function enableRoutes($path = 'routes.php', $middleware = 'web')
    {
        $this->app->router->group(
            [
                'namespace' => $this->getPluginControllerNamespace(),
                'middleware' => $middleware,
            ],
            function ($app) use ($path) {
                require $this->getPluginPath() . DIRECTORY_SEPARATOR . $path;
            }
        );
    }

    /**
     * Register a database migration path for this plugin.
     *
     * @param  array|string  $paths
     * @return void
     */
    protected function enableMigrations($paths = 'migrations')
    {
        $this->app->afterResolving('migrator', function ($migrator) use ($paths) {
            foreach ((array) $paths as $path) {
                $migrator->path($this->getPluginPath() . DIRECTORY_SEPARATOR . $path);
            }
        });
       
    }

        /**
     * Register a database migration path for this plugin.
     *
     * @param  array|string  $paths
     * @return void
     */
    protected function migratePlugin(){
         // create plugins table if it does not exist
         if (!Schema::hasTable('plugins')) {
            Artisan::call('migrate --path=database/migrations/2023_03_08_213703_create_plugins_table.php');

        }

        if(Schema::hasTable('plugins')){
            // check if plugin exists in the database
            $plugin = DB::table('plugins')->where('plugin_identifier', $this->plugin_identifier)->first();

            // if plugin does not exist, insert it into the database
            if (!$plugin) {
                DB::table('plugins')->insert([
                    'name' => $this->name,
                    'domain' => $this->domain,
                    'author' => $this->author,
                    'author_url' => $this->author_url,
                    'description' => $this->description,
                    'directory' => $this->getPluginDir(),
                    'tags' => $this->tags,
                    'plugin_identifier' => $this->plugin_identifier,
                    'purchase_code' => $this->purchase_code,
                    'version' => $this->version,
                    'required_cms_version' => $this->required_cms_version,
                    'status' => false,
                    'license' => $this->license,
                    'license_url' => $this->license_url,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }


    }

    /**
     * Add a translations namespace for this plugin.
     * Eg: __("plugin:articles::{trans_path}")
     *
     * @param string $path
     */
    protected function enableTranslations($path = 'lang')
    {
        $this->app->afterResolving('translator', function ($translator) use ($path) {
            $translator->addNamespace(
                $this->getViewNamespace(),
                $this->getPluginPath() . DIRECTORY_SEPARATOR . $path
            );
        });
    }

    /**
     * @return string
     */
    public function getPluginPath()
    {
        $reflector = $this->getReflector();
        $fileName  = $reflector->getFileName();

        return dirname($fileName);
    }

        /**
     * @return string
     */
    public function getPluginDir()
    {
        $reflector = $this->getReflector();

        return basename(dirname($reflector->getFileName()));
    }

    /**
     * @return string
     */
    protected function getPluginControllerNamespace()
    {
        $reflector = $this->getReflector();
        $baseDir   = str_replace($reflector->getShortName(), '', $reflector->getName());

        return $baseDir . 'Controllers';
    }

    /**
     * @return \ReflectionClass
     */
    private function getReflector()
    {
        if (is_null($this->reflector)) {
            $this->reflector = new \ReflectionClass($this);
        }

        return $this->reflector;
    }

    /**
     * Returns a plugin view
     *
     * @param $view
     * @return \Illuminate\View\View
     */
    protected function view($view)
    {
        return view($this->getViewNamespace() . '::' . $view);
    }

        /**
     * Activate the plugin.
     */
    public function activate()
    {
        DB::table('plugins')->where('plugin_identifier', $this->plugin_identifier)->update([
            'status' => true,
            'updated_at' => now(),
        ]);

        $this->pluginActivated();
    }

    /**
     * Deactivate the plugin.
     */
    public function deactivate()
    {
         DB::table('plugins')->where('plugin_identifier', $this->plugin_identifier)->update([
            'status' => false,
            'updated_at' => now(),
        ]);

        $this->pluginDeactivated();
    }

    /**
     * Check if the plugin is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        $plugin =  DB::table('plugins')->where('plugin_identifier', $this->plugin_identifier)->first();
        return $plugin->status;
    }
}
