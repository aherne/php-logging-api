<?php

namespace Test\Lucinda\Logging\Driver\Stream;

use Lucinda\Logging\ConfigurationException;
use Lucinda\Logging\Driver\Stream\Logger;
use Lucinda\Logging\LogFormatter;
use Lucinda\Logging\RequestInformation;
use Lucinda\UnitTest\Validator\Booleans;
use Lucinda\UnitTest\Validator\Strings;

class LoggerTest
{
    public function writes()
    {
        $root = dirname(__DIR__, 2);
        $code = 'require '.var_export($root.'/vendor/autoload.php', true).'; '
            .'$logger = new \Lucinda\Logging\Driver\Stream\Logger("php://stdout", new \Lucinda\Logging\LogFormatter("%v %m", new \Lucinda\Logging\RequestInformation())); '
            .'$logger->info("stream-message");';
        $output = shell_exec(PHP_BINARY." -r ".escapeshellarg($code));

        return (new Strings($output))->assertEquals(LOG_INFO." stream-message\n");
    }

    public function validatesStream()
    {
        $wasThrown = false;
        try {
            new Logger("/tmp/not-a-stream.log", new LogFormatter("%v %m", new RequestInformation()));
        } catch (ConfigurationException) {
            $wasThrown = true;
        }

        return (new Booleans($wasThrown))->assertTrue("rejects unsupported streams");
    }
}
