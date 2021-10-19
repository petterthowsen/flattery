<?php

namespace ThowsenMedia\Flattery\Data;

use Exception;
use Symfony\Component\Yaml\Yaml;

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

    public function isFileLoaded(string $handle)
    {
        $file = $this->sanitizeFileHandleString($handle);
        return isset($this->_loadedFiles[$file]);
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

    public function getKey(string $file, string $key)
    {
        return array_get($key, $this->_loadedFiles[$file]);
    }

    public function get(string $file, string $key, string $defaultReturnValue = null)
    {
        $this->loadFile($file, false);

        return $this->getKey($file, $key) ?? $defaultReturnValue;
    }
    
    private function startMagicGetting($handle)
    {
        $this->_isMagicGetting = true;
        $this->_magicGettingHandle = $handle;
        $this->_magicGettingKey = null;
        return $this;
    }

}