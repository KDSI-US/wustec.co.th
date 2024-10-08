<?php
/* This file is under Git Control by KDSI. */
namespace Cache;
class Memcached {
	private $expire;
	private $memcached;

	const CACHEDUMP_LIMIT = 9999;

	public function __construct($expire) {
		$this->expire = $expire;
		$this->memcached = new \Memcached();

		$this->memcached->addServer(CACHE_HOSTNAME, CACHE_PORT);
	}


		public function getAllKeys() {
		    if (!method_exists($this->memcached, 'getAllKeys')) {
		        return false;
		    }
			return $this->memcached->getAllKeys();
		}

		public function flush() {
			return $this->memcached->flush();
		}
	public function get($key) {
		return $this->memcached->get(CACHE_PREFIX . $key);
	}

	public function set($key, $value) {
		return $this->memcached->set(CACHE_PREFIX . $key, $value, $this->expire);
	}

	public function delete($key) {
		$this->memcached->delete(CACHE_PREFIX . $key);
	}
}
