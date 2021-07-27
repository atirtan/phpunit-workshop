<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Workshop\Main\SomeClass;

require __DIR__ . '/vendor/autoload.php';

$obj = new SomeClass(new Client(), new Logger('tests', [new StreamHandler('php://stdout', Logger::WARNING)]));

try {
    echo PHP_EOL . $obj->getIpTestable() . PHP_EOL;
} catch (Throwable $t) {
    // just ignore
}

try {
    echo PHP_EOL . $obj->getIpNotTestable() . PHP_EOL;
} catch (Throwable $t) {
    // just ignore
}
