<?php

namespace Test\Lucinda\Logging;

use Lucinda\Logging\RequestInformation;
use Lucinda\Logging\Wrapper;
use Lucinda\UnitTest\Validator\Files;

class MultiLoggerTest
{
    private $logger;

    public function __construct()
    {
        $requestInformation = new RequestInformation();
        $requestInformation->setUserAgent("Chrome");
        $requestInformation->setIpAddress("127.0.0.1");
        $requestInformation->setUri("test");

        $wrapper = new Wrapper(simplexml_load_file("unit-tests.xml"), $requestInformation, "local");
        $this->logger = $wrapper->getLogger();
    }

    public function emergency()
    {
        $throwable = new \Exception("error");
        $this->logger->emergency($throwable);
        return $this->checkErrorLogs(LOG_EMERG, $throwable);
    }


    public function alert()
    {
        $throwable = new \Exception("error");
        $this->logger->alert($throwable);
        return $this->checkErrorLogs(LOG_ALERT, $throwable);
    }


    public function critical()
    {
        $throwable = new \Exception("error");
        $this->logger->critical($throwable);
        return $this->checkErrorLogs(LOG_CRIT, $throwable);
    }


    public function error()
    {
        $throwable = new \Exception("error");
        $this->logger->error($throwable);
        return $this->checkErrorLogs(LOG_ERR, $throwable);
    }


    public function warning()
    {
        $this->logger->warning("message");
        return $this->checkStringLogs(LOG_WARNING, "message");
    }


    public function notice()
    {
        $this->logger->notice("message");
        return $this->checkStringLogs(LOG_NOTICE, "message");
    }


    public function debug()
    {
        $this->logger->debug("message");
        return $this->checkStringLogs(LOG_DEBUG, "message");
    }


    public function info()
    {
        $this->logger->info("message");
        return $this->checkStringLogs(LOG_INFO, "message");
    }

    private function checkStringLogs(int $logLevel, string $message): array
    {
        $info = debug_backtrace(true)[0];
        $result = [];
        $result[] = (new Files("messages__".date("Y-m-d").".log"))->assertContains(date("Y-m-d H:i:s")." ".$logLevel." %e ".$info["file"]." ".($info["line"]-1)." ".$message." test 127.0.0.1 Chrome", "checks file logger");
        $result[] = (new Files("/var/log/syslog"))->assertContains($logLevel." %e ".__FILE__." ".($info["line"]-1)." ".$message." test 127.0.0.1 Chrome", "checks syslogger");
        return $result;
    }

    private function checkErrorLogs(int $logLevel, \Throwable $throwable): array
    {
        $info = debug_backtrace(true)[0];
        $message = $throwable->getMessage();
        $className = get_class($throwable);
        $result = [];
        $result[] = (new Files("messages__".date("Y-m-d").".log"))->assertContains(date("Y-m-d H:i:s")." ".$logLevel." ".$className." ".$info["file"]." ".($info["line"]-2)." ".$message." test 127.0.0.1 Chrome", "checks file logger");
        $result[] = (new Files("/var/log/syslog"))->assertContains($logLevel." ".$className." ".__FILE__." ".($info["line"]-2)." ".$message." test 127.0.0.1 Chrome", "checks syslogger");
        return $result;
    }
}
