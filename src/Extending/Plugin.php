<?php

namespace ThowsenMedia\Flattery\Extending;

use Symfony\Component\Yaml\Yaml;

class Plugin {

    private string $name;

    private array $info = [];

    public final function __construct()
    {
        $this->name = self::class;
    
        # if we have a PluginName.yml file, we'll parse that YAML and save it.
        $infoFile = __DIR__ .'/' .$this->name .'.yml';
        
        if (file_exists($infoFile)) {
            $this->info = Yaml::parseFile($infoFile);
        }
    }

    /**
     * Get info from the PluginName.yml YAML file.
     * 
     * @param string $key the key, use dot-notation, E.G: some.key
     */
    public function getInfo(string $key)
    {
        return array_get($key, $this->info);
    }

    public final function getName()
    {
        return $this->name;
    }

    public function register()
    {

    }

    public function run()
    {

    }

    public function install()
    {

    }

    public function uninstall()
    {

    }

    public function enable()
    {

    }

    public function disable()
    {

    }

}