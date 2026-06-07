<?php

namespace Test\Lucinda\Logging\Driver\Stream;

use Lucinda\Logging\Driver\Stream\Logger;
use Lucinda\Logging\Driver\Stream\Wrapper;
use Lucinda\Logging\RequestInformation;
use Lucinda\UnitTest\Validator\Objects;

class WrapperTest
{
    public function getLogger()
    {
        $xml = simplexml_load_string('<logger stream="php://stdout" format="%v %m"/>');
        $wrapper = new Wrapper($xml, new RequestInformation());

        return (new Objects($wrapper->getLogger()))->assertInstanceOf(Logger::class);
    }
}
