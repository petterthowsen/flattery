<?php

namespace ThowsenMedia\Flattery\Data;

use Exception;
use Symfony\Component\Yaml\Yaml;

/**
 * Reads yml files from the FLATTERY_DATA directory
 * - All data files are lazy-loaded
 * - When setting data, pass true as the second argument to save immediately.
 */
class Data {
    
    private string $_data_dir;
    private array $_loadedFiles = [];

    private bool $_isMagicGetting = false;
    private string $_magicGettingHandle;
    private string $_magicGettingKey;

    public function __construct(string $dataDirectory)
    {
        $this->_data_dir = $dataDirectory;

        if ( ! is_dir($dataDirectory)) {
            throw new \Exception("$dataDirectory does not exist or is not readable.");
        }
    }

    private function sanitizeFileHandleString(string $file): string
    {
        $file = str_replace('.', '/', $file);
        if (str_ends_with($file, '.yml') == false) {
            $file = $file .'.yml';
        }

        return $file;
    }

    public function fileExists(string $handle):bool
    {
        $file = $this->_data_dir .'/' .$this->sanitizeFileHandleString($handle);
        return file_exists($file);
    }

    public function isFileLoaded(string $handle)
    {
        $file = $this->sanitizeFileHandleString($handle);
        return isset($this->_loadedFiles[$file]);
    }

    private function saveFile(string $handle)
    {
        if ( ! isset($this->_loadedFiles[$handle])) {
            throw new \Exception("Cannot save $handle because it's not loaded.");
        }

        $file = $this->sanitizeFileHandleString($handle);
        
        $filePath = $this->_data_dir .'/' .$file;

        $yaml = Yaml::dump($this->_loadedFiles[$handle]);
        if (file_put_contents($filePath, $yaml) === false) {
            throw new \Exception("Failed to write data to $handle");
        }
    }

    private function loadFile(string $handle, bool $forceReload = false)
    {
        if ($this->isFileLoaded($handle) == false or $forceReload == true) {
            $file = $this->sanitizeFileHandleString($handle);

            $filePath = $this->_data_dir .'/' .$file;

            if ( ! file_exists($filePath)) {
                throw new Exception("Cannot load yml file: $file, file does not exist.");
            }
            
            $array = Yaml::parseFile($filePath);
            $this->_loadedFiles[$handle] = $array;
        }
    }

    private function getKey(string $file, string $key)
    {
        return array_get($key, $this->_loadedFiles[$file]);
    }

    private function setKey(string $file, string $key, $value)
    {
        array_set($key, $value, $this->_loadedFiles[$file]);
    }

    public function get(string $file, string $key = null, string $defaultReturnValue = null)
    {
        $this->loadFile($file, false);

        if ($key == null) {
            return $this->_loadedFiles[$file];
        }

        return $this->getKey($file, $key) ?? $defaultReturnValue;
    }
    
    public function has(string $file, string $key): bool
    {
        $this->loadFile($file);
        
        $array = &$this->_loadedFiles[$file];
        
        return array_has($key, $array);
    }
    
    public function contains(string $file, string $key, $needle): bool
    {
        $this->loadFile($file);

        $array = $this->getKey($file, $key);
        return in_array($needle, $array, true);
    }

    public function set(string $file, string $key, $value, bool $save = true)
    {
        $this->loadFile($file);
        $this->setKey($file, $key, $value);

        if ($save) {
            $this->saveFile($file);
        }
    }

}