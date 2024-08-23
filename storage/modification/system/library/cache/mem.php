<?php
/* This file is under Git Control by KDSI. */
namespace Cache;
class Mem {
	private $expire;
	private $memcache;
	
	const CACHEDUMP_LIMIT = 9999;

	public function __construct($expire) {
		$this->expire = $expire;

		$this->memcache = new \Memcache();
		$this->memcache->pconnect(CACHE_HOSTNAME, CACHE_PORT);
	}


		public function getAllKeys() {
		    if (!method_exists($this->memcache, 'getAllKeys')) {
		        return false;
		    }
			return $this->memcache->getAllKeys();
		}

		public function flush() {
			return $this->memcache->flush();
		}
	public function get($key) {
		return $this->memcache->get(CACHE_PREFIX . $key);
	}

	public function set($key, $value) {
		return $this->memcache->set(CACHE_PREFIX . $key, $value, MEMCACHE_COMPRESSED, $this->expire);
	}

	public function delete($key) {
		$this->memcache->delete(CACHE_PREFIX . $key);
	}
}
