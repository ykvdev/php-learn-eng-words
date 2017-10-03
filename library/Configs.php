<?php

namespace Lib;

/**
 * Class Configs
 * @package Lib
 */
class Configs
{
    /** @var array */
    private $configs;

    public function __construct()
    {
        $env = Env::get();

        $confPaths = array_merge(
            glob(APP_ROOT_PATH . '/../config/global/*'),
            glob(APP_ROOT_PATH . '/../config/stage/' . $env . '/*'),
            glob(APP_ROOT_PATH . '/config/global/*'),
            glob(APP_ROOT_PATH . '/config/stage/' . $env . '/*')
        );

        $mergedConfigs = [];
        foreach ($confPaths as $filePath) {
            if (!is_file($filePath)) {
                continue;
            }

            $namespace = str_replace('.php', '', basename($filePath));

            $config = require $filePath;

            if(!empty($mergedConfigs[$namespace])){
                $config = $this->merge($mergedConfigs[$namespace], $config);
            }
            $mergedConfigs[$namespace] = $config;
        }

        $this->configs = $mergedConfigs;
    }

    /**
     * @return array
     */
    public function getConfigs()
    {
        return $this->configs;
    }

    private function merge(array $configMain, array $configAdditional) {
        if(empty($configAdditional)) {
            return $configAdditional;
        }

        foreach($configMain as $key => $value) {
            if(array_key_exists($key, $configAdditional)) {
                if(is_array($configMain[$key]) && is_array($configAdditional[$key])) {
                    $configMain[$key] = $this->merge($configMain[$key], $configAdditional[$key]);
                } else {
                    $configMain[$key] = $configAdditional[$key];
                }
            }
        }

        foreach($configAdditional as $key => $value) {
            if(!array_key_exists($key, $configMain)) {
                $configMain[$key] = $value;
            }
        }

        return $configMain;
    }
}