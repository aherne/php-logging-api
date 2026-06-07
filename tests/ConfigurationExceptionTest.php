<?php

namespace Test\Lucinda\Logging;

use Lucinda\Logging\ConfigurationException;
use Lucinda\UnitTest\Validator\Objects;

class ConfigurationExceptionTest
{
    public function exception()
    {
        return (new Objects(new ConfigurationException("test")))->assertInstanceOf(\Exception::class);
    }
}
