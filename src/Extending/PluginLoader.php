<?php

namespace ThowsenMedia\Flattery\Extending;

use ThowsenMedia\Flattery\Data\Data;

class PluginLoader {

    private string $pluginsDirectory;

    private array $plugins = [];

    private Data $data;

    public function __construct(string $pluginsDirectory)
    {
        $this->pluginsDirectory = $pluginsDirectory;
        $this->data = data();
    }

    public function isLoaded($name)
    {
        return isset($this->plugins[$name]);
    }

    public function loadPlugin(string $name)
    {
        if (isset($this->plugins[$name])) {
            throw new \Exception("Plugin $name is already loaded.");
        }

        $pluginDir = $this->pluginsDirectory .'/' .$name;
        $pluginFileName = $name .'.php';
        $pluginFile = $pluginDir .'/' .$pluginFileName;

        if ( ! is_dir($pluginDir)) {
            throw new \Exception("Plugin directory does not exist: $pluginDir!");
        }else if ( ! file_exists($pluginFile)) {
            throw new \Exception("Plugin class file not found: $pluginFile!");
        }

        require_once $pluginFile;

        if ( ! class_exists($name)) {
            throw new \Exception("$pluginFile does not declare class $name, is it in a namespace?");
        }

        $this->plugins[$name] = new $name();
        
        return new $name();
    }

    /**
     * Load, regsiter and run the given plugins
     */
    public function initialize(array $plugins)
    {
        foreach($plugins as $plugin) {
            $this->loadPlugin($plugin);
        }

        foreach($this->plugins as $plugin) {
            $plugin->register();
        }

        foreach($this->plugins as $plugin) {
            $plugin->run();
        }
    }

    public function isInstalled(string $name): bool
    {
        return $this->data->contains('system.plugins', 'installed', $name);
    }

    public function isEnabled(string $name): bool
    {
        return $this->data->contains('system.plugins', 'enabled', $name);
    }

    /**
     * Install a plugin
     */
    public function install(string $name)
    {
        if ($this->isInstalled($name)) {
            throw new \Exception("$name is already installed.");
        }

        # load the plugin
        $plugin = $this->loadPlugin($name);

        # todo: check the plugin dependencies
        if ($plugin->hasDependencies()) {
            $dependencies = $plugin->getDependencies();
            # verify dependencies are installed...
        }
        
        # let the plugin do it's thing...
        $plugin->install();
        
        $this->data->set('config.system', 'plugins.enabled', $name);
    }

}