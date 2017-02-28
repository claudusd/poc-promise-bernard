<?php

namespace Poc;

use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7\Request;

class Sender
{
    private $pool;

    private $client;

    private $iterator;

    /**
     * @var Promise\PromiseInterface
     */
    private $promise;

    public function __construct()
    {
        $this->client = new Client();
        $this->iterator = new \ArrayIterator();
        $this->pool = new Pool($this->client, $this->iterator, [
            'concurrency' => 4,
            'fulfilled' => function($response, $index)  {
                echo "receive  $index\n";
                if ($index < 20) {
                    echo "Build next Request\n";
                    $this->iterator->append(new Request('GET', 'www.fakeresponse.com/api/?sleep=1'));
                }
            },
            'rejected' => function($reason, $index) {
                echo "rejected $index\n";
            }
        ]);
    }

    public function send($request)
    {
        $this->iterator->append($request);
        if ($this->iterator->count() == 5) {
            $this->promise = $this->pool->promise();
            $this->promiseState();
        }
        $this->wait();
    }

    public function wait()
    {
        if ($this->promise) {
            echo "Wait\n";
            $this->promise->wait();
        }
    }

    public function promiseState()
    {
        if ($this->promise) {
            echo "State : " . $this->promise->getState() . "\n";
        }
    }
}