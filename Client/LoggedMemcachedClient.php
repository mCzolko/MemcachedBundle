<?php

namespace mCzolko\MemcachedBundle\Client;

class LoggedMemcachedClient extends MemcachedClient implements LoggedMemcachedClientInterface
{

    /**
     * @var array
     */
    private $calls = [];

    /**
     * @return array
     */
    public function getLoggedCalls()
    {
        return $this->calls;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        $start = microtime(true);

        $result = parent::get($key);

        $time = microtime(true) - $start;

        $this->calls[] = [
            'action' => 'get',
            'time'   => $time,
            'result' => $result,
            'args'   => [
                'key' => $key
            ]
        ];

        return $result;
    }

    /**
     * @param string $key
     * @param mixed $var
     * @param int|null $expire
     * @return bool
     */
    public function set($key, $var, $expire = null)
    {
        $start = microtime(true);

        $result = parent::set($key, $var, $expire);

        $time = microtime(true) - $start;

        $this->calls[] = [
            'action' => 'set',
            'time'   => $time,
            'result' => $result,
            'args'   => [
                'key' => $key,
                'var' => $var,
                'expire' => $expire
            ]
        ];

        return $result;
    }
}
