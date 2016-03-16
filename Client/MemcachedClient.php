<?php

namespace mCzolko\MemcachedBundle\Client;

use \Memcached;

class MemcachedClient implements MemcachedClientInterface
{

    /**
     * @var Memcached
     */
    private $memcache;

    /**
     * MemcacheClient constructor.
     */
    public function __construct()
    {
        // create a new persistent client
        $this->memcache = new Memcached('memcached_pool');
        $this->memcache->setOption(Memcached::OPT_BINARY_PROTOCOL, TRUE);

        // some nicer default options
        $this->memcache->setOption(Memcached::OPT_NO_BLOCK, TRUE);
        $this->memcache->setOption(Memcached::OPT_AUTO_EJECT_HOSTS, TRUE);
        $this->memcache->setOption(Memcached::OPT_CONNECT_TIMEOUT, 2000);
        $this->memcache->setOption(Memcached::OPT_POLL_TIMEOUT, 2000);
        $this->memcache->setOption(Memcached::OPT_RETRY_TIMEOUT, 2);
    }

    /**
     * @param string $key
     * @param mixed $var
     * @param int $expire
     * @return bool
     */
    public function set($key, $var, $expire = null)
    {
        return $this->memcache->set($key, $var, $expire);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->memcache->get($key);
    }

    /**
     * Setup authentication.
     *
     * @param string $username
     * @param string $password
     */
    public function setAuthData($username, $password)
    {
        $this->memcache->setSaslAuthData($username, $password);
    }

    /**
     * @return bool
     */
    public function hasServers()
    {
        return (bool) $this->memcache->getServerList();
    }

    /**
     * @param string $host
     * @param int $port
     */
    public function addServer($host, $port)
    {
        $this->memcache->addServer($host, $port);
    }
}
