<?php

namespace mCzolko\MemcachedBundle\Client;

interface MemcachedClientInterface
{

    /**
     * @param string $key
     * @param mixed $var
     * @param int $expire
     * @return mixed
     */
    public function set($key, $var, $expire = null);

    /**
     * @param string $key
     * @return mixed
     */
    public function get($key);
}
