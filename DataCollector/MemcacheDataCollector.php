<?php

namespace mCzolko\MemcachedBundle\DataCollector;

use mCzolko\MemcachedBundle\Client\LoggedMemcachedClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class MemcacheDataCollector extends DataCollector
{

    /**
     * @var LoggedMemcachedClientInterface
     */
    private $loggedMemcachedClient;

    /**
     * @param LoggedMemcachedClientInterface $loggedMemcachedClient
     */
    public function addClient(LoggedMemcachedClientInterface $loggedMemcachedClient)
    {
        $this->loggedMemcachedClient = $loggedMemcachedClient;
    }

    /**
     * Collects data for the given Request and Response.
     *
     * @param Request $request A Request instance
     * @param Response $response A Response instance
     * @param \Exception $exception An Exception instance
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $calls = $this->loggedMemcachedClient->getLoggedCalls();

        list($reads, $hits, $writes, $totalTime) = $this->getCounts($calls);

        $this->data['calls'] = $calls;
        $this->data['totals'] = [
            'calls' => count($calls),
            'reads' => $reads,
            'hits'   => $hits,
            'writes' => $writes,
            'time' => $totalTime
        ];

        $this->data['totals']['ratio'] = 0;
        if ($reads) $this->data['totals']['ratio'] = 100 * $hits / $reads;
    }

    /**
     * @param array $calls
     * @return int
     */
    private function getCounts(array $calls)
    {
        $reads = $hits = $writes = $totalTime = 0;

        foreach ($calls as $call) {
            if ($call['action'] == 'get') {
                $reads++;

                if ($call['result'] !== false)
                    $hits++;
            }

            if ($call['action'] == 'set')
                $writes++;

            $totalTime += $call['time'];

        }

        return [$reads, $hits, $writes, $totalTime];
    }

    /**
     * @return array
     */
    public function getCalls()
    {
        return $this->data['calls'];
    }

    /**
     * @param string $key
     * @return string
     */
    public function getResult($key)
    {
        return $this->varToString($this->data['calls'][$key]['result']);
    }

    /**
     * @param string $key
     * @return string
     */
    public function getArguments($key)
    {
        unset($this->data['calls'][$key]['args']['key']);

        if ($this->data['calls'][$key]['args']) {
            return $this->varToString($this->data['calls'][$key]['args']);
        }

        return '';
    }

    /**
     * @return array
     */
    public function getTotals()
    {
        return $this->data['totals'];
    }

    /**
     * Returns the name of the collector.
     *
     * @return string The collector name
     */
    public function getName()
    {
        return 'memcache';
    }
}
