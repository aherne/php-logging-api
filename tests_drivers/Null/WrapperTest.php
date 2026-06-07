<?php

namespace Test\Lucinda\Logging\Driver\Null;

use Lucinda\Logging\Driver\Null\Logger;
use Lucinda\Logging\Driver\Null\Wrapper;
use Lucinda\Logging\RequestInformation;
use Lucinda\UnitTest\Validator\Objects;

class WrapperTest
{
    public function getLogger()
    {
        $wrapper = new Wrapper(simplexml_load_string('<logger/>'), new RequestInformation());

        return (new Objects($wrapper->getLogger()))->assertInstanceOf(Logger::class);
    }
}
