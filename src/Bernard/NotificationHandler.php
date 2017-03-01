<?php

namespace Poc\Bernard;

use Bernard\Message\DefaultMessage;
use GuzzleHttp\Psr7\Request;

class NotificationHandler
{
    private $arrayIterator;

    /**
     * NotificationHandler constructor.
     * @param \ArrayIterator $arrayIterator
     */
    public function __construct(\ArrayIterator $arrayIterator)
    {
        $this->arrayIterator = $arrayIterator;
    }

    public function notification(DefaultMessage $message)
    {
        $this->arrayIterator->append(new Request('GET', $message->url));
    }
}