<?php

namespace Test\Lucinda\Logging\Driver\Json;

use Lucinda\Logging\Driver\Json\Logger;
use Lucinda\Logging\RequestInformation;
use Lucinda\UnitTest\Validator\Arrays;
use Lucinda\UnitTest\Validator\Files;

class LoggerTest
{
    public function writes()
    {
        $filePath = sys_get_temp_dir()."/lucinda_json_logger_test";
        $fileName = $filePath."__".date("Y-m-d").".json";
        if (file_exists($fileName)) {
            unlink($fileName);
        }

        $requestInformation = new RequestInformation();
        $requestInformation->setUri("/json");
        $logger = new Logger($filePath, $requestInformation, "Y-m-d");
        $logger->info("json-message");

        $payload = json_decode(trim(file_get_contents($fileName)), true);

        return [
            (new Files($fileName))->assertExists("creates json log file"),
            (new Arrays($payload))->assertContainsKey("timestamp", "writes timestamp"),
            (new Arrays($payload))->assertContainsKey("level", "writes level"),
            (new Arrays($payload))->assertContainsKey("message", "writes message"),
            (new Arrays($payload))->assertContainsValue("json-message", "writes message value"),
            (new Arrays($payload))->assertContainsValue("/json", "writes request uri")
        ];
    }
}
