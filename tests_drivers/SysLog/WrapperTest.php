<?php

namespace Test\Lucinda\Logging\Driver\SysLog;

use Lucinda\Logging\Driver\SysLog\Logger;
use Lucinda\Logging\Driver\SysLog\Wrapper;
use Lucinda\Logging\RequestInformation;
use Lucinda\UnitTest\Validator\Objects;

class WrapperTest
{
    public function getLogger()
    {
        $xml = simplexml_load_string('<logger application="unittest" format="%v %m"/>');
        $wrapper = new Wrapper($xml, new RequestInformation());

        return (new Objects($wrapper->getLogger()))->assertInstanceOf(Logger::class);
    }
}
