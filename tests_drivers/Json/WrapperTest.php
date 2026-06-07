<?php

namespace Test\Lucinda\Logging\Driver\Json;

use Lucinda\Logging\Driver\Json\Logger;
use Lucinda\Logging\Driver\Json\Wrapper;
use Lucinda\Logging\RequestInformation;
use Lucinda\UnitTest\Validator\Objects;

class WrapperTest
{
    public function getLogger()
    {
        $xml = simplexml_load_string('<logger path="'.sys_get_temp_dir().'/lucinda_json_wrapper_test"/>');
        $wrapper = new Wrapper($xml, new RequestInformation());

        return (new Objects($wrapper->getLogger()))->assertInstanceOf(Logger::class);
    }
}
