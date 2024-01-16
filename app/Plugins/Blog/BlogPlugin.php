<?php

namespace App\Plugins\Blog;

use App\PluginManager\Plugin;

class BlogPlugin extends Plugin
{
    public $name = 'Blog';
    public $description = 'This is a description';
    public $version = '1.0.0';
    public $author = 'Spa Green';
    public $author_url = 'https://spagreen.net';
    public $tag = 'Blog, Plugin, Demo';
    public $plugin_identifier ='blog_plugin';
    public $required_cms_version ='1.6.4';
    public $license ='General Public License';
    public $license_url ='https://mit-license.org/GPL';

    public function boot()
    {
        // $this->enableViews();
        // $this->enableRoutes();
    }
}
