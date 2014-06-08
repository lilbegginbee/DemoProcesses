<?php

class CORE_Cache_Driver_Memory {

    const LT_DAY = 86400;
    const LT_1MIN = 60;
    const LT_HOUR = 3600;
    const LT_WEAK = 604800;
    const LT_MOUNTH = 2592000;
    const LT_2MOUNTH = 5184000;
    const LT_3MOUNTH = 7776000;
    const LT_YEAR = 31104000;

    const Cacher_Memcached = 'memcached';
    const Cacher_Redis = 'redis';

    private static $_instance = null;
    private $_cacher = null;


    private function __construct( $cacher )
    {
        $this->_cacher = $cacher;
    }

    public static function getInstance($cacher = null)
    {
        if( is_null( self::$_instance ) ) {
            /**
             * @var CORE_Cache_Driver_Memory
             */
            self::$_instance = new CORE_Cache_Driver_Memory( $cacher );
        }

        return self::$_instance;
    }

    public function set( $key, $value, $ttl = null )
    {
        return $this->_cacher->set( $key, $value, $ttl );
    }

    public function get( $key )
    {
        return $this->_cacher->get( $key );
    }

    public function delete( $key )
    {
        return $this->_cacher->delete( $key );
    }

    /**
     * Попытка создать централизованую обработку ключей кеширования
     * @param $pattern
     * @return string
     */
    public static function generateKey($pattern)
    {
        $args = func_get_args();
        $args_count = count($args);
        if( $args_count > 1 ){
            for( $i=1; $i<$args_count; $i++ ) {
                $pattern = sprintf( $pattern, $args[$i]);
            }
        }

        return $pattern;
    }

}