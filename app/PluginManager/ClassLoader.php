<?php
namespace App\PluginManager;

class ClassLoader
{
    /**
     * @var PluginManager
     */
    protected $pluginManager;

    /**
     * ClassLoader constructor.
     *
     * @param PluginManager $pluginManager
     */
    public function __construct(PluginManager $pluginManager)
    {
        $this->pluginManager = $pluginManager;
    }

    /**
     * Loads the given class or interface.
     *
     * @param $class
     * @return bool|null
     */
    public function loadClass($class)
    {
        if (isset($this->pluginManager->getClassMap()[$class])) {
            include($this->pluginManager->getClassMap()[$class]);

            return true;
        }
    }
}
