<?php

namespace Test\Lucinda\Logging;

use Lucinda\Logging\LogFormatter;
use Lucinda\Logging\RequestInformation;
use Lucinda\UnitTest\Validator\Strings;

class LogFormatterTest
{
    public function format()
    {
        $requestInformation = new RequestInformation();
        $requestInformation->setUserAgent("Chrome");
        $requestInformation->setIpAddress("127.0.0.1");
        $requestInformation->setUri("test");

        $formatter = new LogFormatter("%d %v %m %u %i %a", $requestInformation);
        $stringResult = $formatter->format("message", LOG_INFO);

        $exception = new \Exception("testing");
        $formatter = new LogFormatter("%d %v %e %f %l %m %u %i %a", $requestInformation);
        $exceptionResult = $formatter->format($exception, LOG_EMERG);

        return [
            (new Strings($stringResult))->assertEquals(date("Y-m-d H:i:s")." ".LOG_INFO." message test 127.0.0.1 Chrome", "formats string log line"),
            (new Strings($exceptionResult))->assertContains(LOG_EMERG." Exception ".__FILE__, "formats exception file"),
            (new Strings($exceptionResult))->assertContains(" testing test 127.0.0.1 Chrome", "formats exception message and request")
        ];
    }
}
