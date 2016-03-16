<?php

namespace mCzolko\MemcachedBundle\Client;

interface LoggedMemcachedClientInterface extends MemcachedClientInterface
{

    /**
     * @return array
     */
    public function getLoggedCalls();
}
