<?php

class CORE_Cache_Driver_Memcache
{
	public $_cache = null;

	protected static $_host = null;
	protected static $_port = null;
	protected static $_instance = null;

	private function __construct()
	{
		$this->_cache = new Memcache;

		if (is_null(self::$_host)) {
			self::$_host = Zend_Registry::get('config')->memcached->host;

            if (!self::$_host) {
				self::$_host = '127.0.0.1';
			}
		}
		
		if (is_null(self::$_port)) {
			self::$_port = Zend_Registry::get('config')->memcached->port;

			if (!self::$_port) {
				self::$_port = '11211';
			}
		}
		
		$this->_cache->connect(self::$_host, self::$_port);
		$this->_cache->setCompressThreshold(2000000000, 0);
	}

    public static function isEnabled()
    {
        try {
            if (Zend_Registry::get('config')->memcached->enabled == 1) {
                return true;
            }
        } catch (Exception $e) {}

        return false;
    }

	public static function init($host, $port = 11211)
	{
		if (!is_null($host)) {
			self::$_host = $host;
			self::$_port = $port;
		}
	}
	
	public static function getInstance() {
		if (is_null(self::$_instance)) {
    	    self::$_instance = new CORE_Cache_Driver_Memcache();
		}

		return self::$_instance;
	}
	
	public static function set($cache_key, $data, $life_time = CORE_Cache_Driver_Memory::LT_HOUR, $tags = array())
	{
        if (!self::isEnabled()) {
            return false;
        }

		$serialized_data = serialize($data);
        if (is_array($tags) && count($tags) > 0) {
            foreach($tags AS $tag) {
                $tag_info = self::get("tag_{$tag}");
                if (!is_array($tag_info)) {
                    $tag_info = array();
                }

                if (!in_array($cache_key, $tag_info)) {
                    array_push($tag_info, $cache_key);
                    self::getInstance()->_cache->set("tag_{$tag}", $tag_info, 0, 0);
                }
            }
        }

		if (mb_strlen($serialized_data, 'UTF-8') > 1000000) {
			// объект слишком большой, разбиваем его.
			$tmp = str_split($serialized_data, 1000000);
			if (is_array($tmp) && count($tmp) > 0) {
				$ch_obj = new cacheChunk();
				foreach ($tmp AS $k => $chunk) {
					self::getInstance()->_cache->set("{$cache_key}_{$k}", $chunk, 0, $life_time);
					$ch_obj->set("{$cache_key}_{$k}");
				}

				return self::getInstance()->_cache->set($cache_key, $ch_obj, 0, $life_time);
			}
			
		} else {
			return self::getInstance()->_cache->set($cache_key, $data, 0, $life_time);
		}
	}

    public static function removeByTag($tags)
    {
        if (is_string($tags)) {
            $tags = array($tags);
        }

        if (is_array($tags) && count($tags) > 0) {
            foreach ($tags AS $tag) {
                $tag_info = self::get("tag_{$tag}");
                if (is_array($tag_info) && count($tag_info) > 0) {
                    foreach($tag_info AS $cache_key) {
                        self::delete($cache_key);
                    }
                }
            }
        }
    }
	
	public static function get($cache_key)
	{
        if(!self::isEnabled()) {
            return false;
        }

		$item = self::getInstance()->_cache->get($cache_key);
		if (!is_object($item)) {
            return $item;
        }

		if (get_class($item) == 'cacheChunk') {
			// объект хранится в разобранном виде
			$chunks = $item->get();
			if (is_array($chunks) && count($chunks) > 0) {
				$obj_str = '';
				foreach ($chunks AS $key) {
					$obj_str .= self::getInstance()->_cache->get($key);
				}

				$obj = @unserialize($obj_str);
				
				if ($obj !== false) {
					return $obj;
				} else {
					// произошла проблема в кеше. чистим его по ключу.
					self::delete($cache_key);
					return;
				}
			}
		} else {
			return $item;
		}
	}
	
	public static function delete($cache_key)
	{
        if (!self::isEnabled()) {
            return false;
        }

		return self::getInstance()->_cache->delete($cache_key);
	}	
	
	public static function cleanAll()
	{
		return self::getInstance()->_cache->flush();
	}
}

/**
 * Для хранения больших объектов в мемкеше.
 * 
 * @author galem
 *
 */
class cacheChunk
{
	protected $_data = array();
	
	public function __construct() {}
	
	public function set($key)
	{
		$this->_data[] = $key;
	}
	
	public function get()
	{
		return $this->_data;
	}
}