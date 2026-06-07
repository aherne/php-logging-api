<?php

namespace Lucinda\Logging;

/**
 * Formats log message for text-based loggers (eg: syslog or file) based on a combination of following placeholders:
 * - %d: timestamp message has occurred
 * - %v: syslog priority level
 * - %e: thrown exception class
 * - %f: thrown exception file
 * - %l: thrown exception line
 * - %m: thrown exception message
 * - %u: uri in which message has occurred
 */
class LogFormatter
{
    private string $pattern;
    private RequestInformation $requestInformation;

    /**
     * Creates instance and saves message pattern.
     *
     * @param string             $pattern            Log message pattern (eg: %d %u %f %l %m)
     * @param RequestInformation $requestInformation
     */
    public function __construct(string $pattern, RequestInformation $requestInformation)
    {
        $this->pattern = $pattern;
        $this->requestInformation = $requestInformation;
    }

    /**
     * Builds log message based on global pattern and info to be logged.
     *
     * @param  string|\Throwable $info  Information that needs being logged
     * @param  integer           $level Log level (see: https://tools.ietf.org/html/rfc5424)
     * @return string Compiled log message ready to be saved.
     */
    public function format(string|\Throwable $info, int $level): string
    {
        $message = $this->pattern;
        $message = str_replace("%d", date("Y-m-d H:i:s"), $message);
        $message = str_replace("%v", $level, $message);
        if ($info instanceof \Throwable) {
            $message = str_replace("%e", get_class($info), $message);
            $message = str_replace("%f", $info->getFile(), $message);
            $message = str_replace("%l", $info->getLine(), $message);
            $message = str_replace("%m", $info->getMessage(), $message);
        } else {
            $message = str_replace("%m", $info, $message);
            $trace = debug_backtrace();
            foreach ($trace as $line) {
                if (($line["class"] ?? "")=="Lucinda\Logging\Logger") {
                    $message = str_replace("%f", $line["file"], $message);
                    $message = str_replace("%l", $line["line"], $message);
                    break;
                }
            }
        }
        if ($requestURI = $this->requestInformation->getUri()) {
            $message = str_replace("%u", $requestURI, $message);
        }
        if ($ipAddress = $this->requestInformation->getIpAddress()) {
            $message = str_replace("%i", $ipAddress, $message);
        }
        if ($userAgent = $this->requestInformation->getUserAgent()) {
            $message = str_replace("%a", $userAgent, $message);
        }
        return $message;
    }
}
