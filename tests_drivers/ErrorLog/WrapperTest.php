<?php

namespace Test\Lucinda\Logging\Driver\ErrorLog;

use Lucinda\Logging\Driver\ErrorLog\Logger;
use Lucinda\Logging\Driver\ErrorLog\Wrapper;
use Lucinda\Logging\RequestInformation;
use Lucinda\UnitTest\Validator\Objects;

class WrapperTest
{
    public function getLogger()
    {
        $xml = simplexml_load_string('<logger format="%v %m"/>');
        $wrapper = new Wrapper($xml, new RequestInformation());

        return (new Objects($wrapper->getLogger()))->assertInstanceOf(Logger::class);
    }
}
