<?php

namespace Test\Lucinda\Logging;

use Lucinda\Logging\MultiLogger;
use Lucinda\Logging\RequestInformation;
use Lucinda\Logging\Wrapper;
use Lucinda\UnitTest\Validator\Objects;

class WrapperTest
{
    public function getLogger()
    {
        $xml = simplexml_load_string(
            '<xml><loggers><local><logger class="Lucinda\Logging\Driver\Null\Wrapper"/></local></loggers></xml>'
        );
        $wrapper = new Wrapper($xml, new RequestInformation(), "local");

        return (new Objects($wrapper->getLogger()))->assertInstanceOf(MultiLogger::class);
    }
}
