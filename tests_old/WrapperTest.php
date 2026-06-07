<?php

namespace Test\Lucinda\Logging;

use Lucinda\Logging\RequestInformation;
use Lucinda\Logging\Wrapper;
use Lucinda\UnitTest\Validator\Files;

class WrapperTest
{
    public function getLogger()
    {
        $requestInformation = new RequestInformation();
        $requestInformation->setUserAgent("Chrome");
        $requestInformation->setIpAddress("127.0.0.1");
        $requestInformation->setUri("test");

        $wrapper = new Wrapper(simplexml_load_file("unit-tests.xml"), $requestInformation, "local");
        $logger = $wrapper->getLogger();
        $result = [];
        $logger->emergency(new \Exception("error"));
        $result[] = (new Files("messages__".date("Y-m-d").".log"))->assertContains(date("Y-m-d H:i:s")." ".LOG_EMERG." Exception ".__FILE__." ".(__LINE__-1)." error test 127.0.0.1 Chrome", "checks file logger");
        $result[] = (new Files("/var/log/syslog"))->assertContains(LOG_EMERG." Exception ".__FILE__." ".(__LINE__-2)." error test 127.0.0.1 Chrome", "checks syslogger");
        return $result;
    }
}
