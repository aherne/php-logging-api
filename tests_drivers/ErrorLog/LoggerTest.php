<?php

namespace Test\Lucinda\Logging\Driver\ErrorLog;

use Lucinda\Logging\Driver\ErrorLog\Logger;
use Lucinda\Logging\LogFormatter;
use Lucinda\Logging\RequestInformation;
use Lucinda\UnitTest\Validator\Files;

class LoggerTest
{
    public function writes()
    {
        $fileName = sys_get_temp_dir()."/lucinda_error_log_test.log";
        if (file_exists($fileName)) {
            unlink($fileName);
        }
        ini_set("log_errors", "1");
        ini_set("error_log", $fileName);

        $logger = new Logger(new LogFormatter("%v %m", new RequestInformation()));
        $logger->info("error-log-message");

        return [
            (new Files($fileName))->assertExists("creates error log file"),
            (new Files($fileName))->assertContains(LOG_INFO." error-log-message", "writes formatted line")
        ];
    }
}
