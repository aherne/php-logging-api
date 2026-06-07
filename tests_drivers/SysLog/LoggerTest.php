<?php

namespace Test\Lucinda\Logging\Driver\SysLog;

use Lucinda\Logging\Driver\SysLog\Logger;
use Lucinda\Logging\LogFormatter;
use Lucinda\Logging\RequestInformation;
use Lucinda\UnitTest\Validator\Objects;

class LoggerTest
{
    public function create()
    {
        $logger = new Logger("unittest", new LogFormatter("%v %m", new RequestInformation()));

        return (new Objects($logger))->assertInstanceOf(Logger::class);
    }
}
