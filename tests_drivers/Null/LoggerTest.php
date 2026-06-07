<?php

namespace Test\Lucinda\Logging\Driver\Null;

use Lucinda\Logging\Driver\Null\Logger;
use Lucinda\UnitTest\Validator\Objects;

class LoggerTest
{
    public function discards()
    {
        $logger = new Logger();
        $logger->info("ignored");

        return (new Objects($logger))->assertInstanceOf(Logger::class);
    }
}
