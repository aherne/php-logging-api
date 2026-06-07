<?php

namespace Lucinda\Logging\Driver\SysLog;

use Lucinda\Logging\LogFormatter;

/**
 * Logs messages/errors into syslog service.
 */
final class Logger extends \Lucinda\Logging\Logger
{
    private string $applicationName;
    private LogFormatter $formatter;

    /**
     * Creates a logger instance.
     *
     * @param string       $applicationName Name of your application to appear in log lines.
     * @param LogFormatter $formatter       Class responsible in creating and formatting logging message.
     */
    public function __construct(string $applicationName, LogFormatter $formatter)
    {
        $this->applicationName = $applicationName;
        $this->formatter = $formatter;
    }

    /**
     * Performs the act of logging.
     *
     * @param string|\Throwable $info  Information that needs being logged
     * @param integer           $level Log level (see: https://tools.ietf.org/html/rfc5424)
     */
    protected function log(string|\Throwable $info, int $level): void
    {
        openlog($this->applicationName, LOG_NDELAY, LOG_USER);
        syslog($level, $this->formatter->format($info, $level));
        closelog();
    }
}
