<?php

namespace Poc;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class Run
{
    private $sender;

    public function __construct()
    {
        $this->sender = new Sender();
        $this->client = new Client();
    }

    public function run($count) {
        $count = 2;
        for($i = 1; $i <= $count; $i++) {
            echo "yolo\n";
            $this->sender->send(new Request('GET', 'www.fakeresponse.com/api/?sleep=1'));
        }
    }
}