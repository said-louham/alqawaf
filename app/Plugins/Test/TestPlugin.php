<?php

namespace App\Plugins\Test;

use App\PluginManager\Plugin;

class TestPlugin extends Plugin
{
    public $name = 'Test';
    public $description = 'This is a description';
    public $version = '1.0.0';
    public $author = 'Spa Green';
    public $author_url = 'https://spagreen.net';
    public $tag = 'Test, Plugin, Demo';
    public $plugin_identifier ='test_plugin';
    public $required_cms_version ='1.6.4';
    public $license ='General Public License';
    public $license_url ='https://mit-license.org/GPL';

    public function boot()
    {
        $this->enableViews();
        $this->enableRoutes();
    }

    public function pluginActivated()
    {
        dd('I am activated');
    }

    public function pluginDeactivated()
    {
        dd('I am deActivated');
    }
}
