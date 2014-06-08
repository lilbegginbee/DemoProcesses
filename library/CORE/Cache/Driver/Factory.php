<?php

class CORE_Cache_Driver_Factory
{
    const TYPE_MEMCACHED = 'memcached';
    const TYPE_REDIS = 'redis';

    public static function createCacher($cacherType = '')
    {
        $config = Zend_Registry::get('config');

        switch($cacherType) {
            case self::TYPE_MEMCACHED:
                CORE_Cache_Driver_Memcache::init($config->memcached->host, $config->memcached->port);
                $cacher = CORE_Cache_Driver_Memcache::getInstance();
                break;
            case self::TYPE_REDIS:
                $cacher = new CORE_Cache_Driver_Redis($config->redis->host, $config->redis->port);
                break;
            default:
                throw new Exception('Cacher type is unknown');
        }

        $cacher = CORE_Cache_Driver_Memory::getInstance($cacher);

        return $cacher;
    }
}