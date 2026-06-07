<?php

namespace Test\Lucinda\Logging;

use Lucinda\Logging\LogFormatter;
use Lucinda\Logging\RequestInformation;
use Lucinda\UnitTest\Result;

class LogFormatterTest
{
    public function format()
    {
        $requestInformation = new RequestInformation();
        $requestInformation->setUserAgent("Chrome");
        $requestInformation->setIpAddress("127.0.0.1");
        $requestInformation->setUri("test");

        $results = [];

        $formatter = new LogFormatter("%d %v %u %i %a", $requestInformation);
        $result = $formatter->format("message", LOG_INFO);
        $results[] = new Result($result == date("Y-m-d H:i:s")." ".LOG_INFO." test 127.0.0.1 Chrome", "checks string log line");

        $formatter = new LogFormatter("%d %v %e %f %l %m %u %i %a", $requestInformation);
        $result = $formatter->format(new \Exception("testing"), LOG_EMERG);
        $results[] = new Result($result == date("Y-m-d H:i:s")." ".LOG_EMERG." Exception ".__FILE__." ".(__LINE__-1)." testing test 127.0.0.1 Chrome", "checks exception log line");

        return $results;
    }
}
