#!/usr/bin/env php
<?php
include_once __DIR__.'/../vendor/autoload.php';

$runner = new \Poc\Run();
$runner->run(10);