<?php

namespace Poc\Mail;

use Bernard\QueueFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Promise\Promise;
use Poc\Bernard\Consumer;

class Sender
{
    /**
     * @var Consumer
     */
    private $consumer;

    private $concurrency;

    /**
     * @var \Bernard\Queue
     */
    private $queue;

    /**
     * @var Pool
     */
    private $pool;

    public function __construct(Consumer $consumer, QueueFactory $queueFactory, $concurrency, \ArrayIterator $requestStack)
    {

        $this->consumer = $consumer;
        $this->concurrency = $concurrency;
        $this->queue = $queueFactory->create('notification');
        $this->pool = new Pool(new Client(), $requestStack, [
            'concurrency' => $this->concurrency,
            'fulfilled' => function($response, $index) use ($requestStack) {
                $request = $requestStack[$index];
                $this->consumer->tick($this->queue);
            },
            'rejected' => function($reason, $index) use ($requestStack) {
                $request = $requestStack[$index];
            }
        ]);
    }

    /**
     *
     */
    public function send()
    {
        for ($i =0; $i < $this->concurrency; $i++) {
            $this->consumer->tick($this->queue);
        }
        $promise = $this->pool->promise();
        $promise->wait();
    }
}