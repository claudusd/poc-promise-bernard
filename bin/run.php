#!/usr/bin/env php
<?php

include_once __DIR__.'/../vendor/autoload.php';

$container = new \Pimple\Container();

//BernardPHP


$container['bernard.producer'] = function($container) {
    return new \Bernard\Producer(
        $container['bernard.queue_factory.inmemeory'],
        $container['bernard.producer.middleware_builder']
    );
};

$container['bernard.queue_factory.inmemeory'] = function() {
    return new \Bernard\QueueFactory\InMemoryFactory();  
};

$container['bernard.producer.middleware_builder'] = function() {
    return new \Bernard\Middleware\MiddlewareBuilder();
};

$container['bernard.consumer.middleware_builder'] = function() {
    return new \Bernard\Middleware\MiddlewareBuilder();
};

$container['request_stack'] = function() {
    return new ArrayIterator();
};

$container['bernard.handler.notification'] = function($container) {
    return new \Poc\Bernard\NotificationHandler(
        $container['request_stack']
    );
};

$container['bernard.router'] = function($container) {
    $router = new \Bernard\Router\SimpleRouter();
    $router->add('notification', $container['bernard.handler.notification']);
    return $router;
};

$container['bernard.consumer'] = function($container) {
    return new \Poc\Bernard\Consumer(
        $container['bernard.consumer.middleware_builder'],
        $container['bernard.router']
    );
};

// SendMail
$container['mail.sender'] = function($container) {
    return new \Poc\Mail\Sender(
        $container['bernard.consumer'],
        $container['bernard.queue_factory.inmemeory'],
        $container['mail.sender.concurrency'],
        $container['request_stack']
    );
};

$container['mail.sender.concurrency'] = 7;

for ($i = 0; $i<50; $i++) {
    $container['bernard.producer']->produce(
        new \Bernard\Message\DefaultMessage('notification', ['url'=>'www.fakeresponse.com/api/?sleep=2'])
    );
}

$container['mail.sender']->send();
