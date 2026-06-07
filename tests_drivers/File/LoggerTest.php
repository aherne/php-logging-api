<?php

namespace Test\Lucinda\Logging\Driver\File;

use Lucinda\Logging\Driver\File\Logger;
use Lucinda\Logging\LogFormatter;
use Lucinda\Logging\RequestInformation;
use Lucinda\UnitTest\Validator\Files;

class LoggerTest
{
    public function writes()
    {
        $filePath = sys_get_temp_dir()."/lucinda_file_logger_test";
        $fileName = $filePath."__".date("Y-m-d").".log";
        if (file_exists($fileName)) {
            unlink($fileName);
        }

        $logger = new Logger($filePath, new LogFormatter("%v %m", new RequestInformation()), "Y-m-d");
        $logger->info("file-message");

        return [
            (new Files($fileName))->assertExists("creates log file"),
            (new Files($fileName))->assertContains(LOG_INFO." file-message", "writes formatted line")
        ];
    }
}
