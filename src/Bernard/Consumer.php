<?php

namespace Poc\Bernard;

use Bernard\Envelope;
use Bernard\Middleware;
use Bernard\Middleware\MiddlewareBuilder;
use Bernard\Queue;
use Bernard\Router;

class Consumer implements Middleware
{
    /**
     * @var MiddlewareBuilder
     */
    private $middlewareBuilder;

    /**
     * @var Router
     */
    private $router;

    /**
     * Consumer constructor.
     * @param MiddlewareBuilder $middlewareBuilder
     * @param Router $router
     */
    public function __construct(MiddlewareBuilder $middlewareBuilder, Router $router)
    {
        $this->middlewareBuilder = $middlewareBuilder;
        $this->router = $router;
    }

    public function call(Envelope $envelope, Queue $queue)
    {
        call_user_func($this->router->map($envelope), $envelope->getMessage());

        $queue->acknowledge($envelope);
    }

    public function tick(Queue $queue)
    {
        if (!$envelope = $queue->dequeue()) {
            return true;
        }
        $this->invoke($envelope, $queue);
        return true;
    }

    public function invoke(Envelope $envelope, Queue $queue)
    {
        try {
            $middleware = $this->middlewareBuilder->build($this);
            $middleware->call($envelope, $queue);
        } catch (\Exception $e) {

        }
    }
}