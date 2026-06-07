<?php

namespace Test\Lucinda\Logging;

use Lucinda\Logging\Logger;
use Lucinda\Logging\MultiLogger;
use Lucinda\UnitTest\Validator\Arrays;

class MultiLoggerTest
{
    private object $first;
    private object $second;
    private MultiLogger $logger;

    public function __construct()
    {
        $this->first = new class () extends Logger {
            public array $records = [];

            protected function log(string|\Throwable $info, int $level): void
            {
                $this->records[] = [$level, $info instanceof \Throwable ? $info->getMessage() : $info];
            }
        };
        $this->second = new class () extends Logger {
            public array $records = [];

            protected function log(string|\Throwable $info, int $level): void
            {
                $this->records[] = [$level, $info instanceof \Throwable ? $info->getMessage() : $info];
            }
        };
        $this->logger = new MultiLogger([$this->first, $this->second]);
    }

    public function delegates()
    {
        $this->logger->info("message");

        return $this->assertRecords(LOG_INFO, "message");
    }
    public function emergency()
    {
        $this->logger->emergency(new \Exception("emergency"));
        return $this->assertRecords(LOG_EMERG, "emergency");
    }
        

    public function alert()
    {
        $this->logger->alert(new \Exception("alert"));
        return $this->assertRecords(LOG_ALERT, "alert");
    }
        

    public function critical()
    {
        $this->logger->critical(new \Exception("critical"));
        return $this->assertRecords(LOG_CRIT, "critical");
    }
        

    public function error()
    {
        $this->logger->error(new \Exception("error"));
        return $this->assertRecords(LOG_ERR, "error");
    }
        

    public function warning()
    {
        $this->logger->warning("warning");
        return $this->assertRecords(LOG_WARNING, "warning");
    }
        

    public function notice()
    {
        $this->logger->notice("notice");
        return $this->assertRecords(LOG_NOTICE, "notice");
    }
        

    public function debug()
    {
        $this->logger->debug("debug");
        return $this->assertRecords(LOG_DEBUG, "debug");
    }
        

    public function info()
    {
        $this->logger->info("info");
        return $this->assertRecords(LOG_INFO, "info");
    }

    private function assertRecords(int $level, string $message): array
    {
        $expected = [[$level, $message]];
        $first = array_slice($this->first->records, -1);
        $second = array_slice($this->second->records, -1);

        return [
            (new Arrays($first))->assertEquals($expected, "delegates to first logger"),
            (new Arrays($second))->assertEquals($expected, "delegates to second logger")
        ];
    }
}
