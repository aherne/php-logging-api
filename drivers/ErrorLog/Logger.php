<?php

namespace Lucinda\Logging\Driver\ErrorLog;

use Lucinda\Logging\LogFormatter;

/**
 * Logs messages/errors through PHP's native error_log facility.
 */
final class Logger extends \Lucinda\Logging\Logger
{
    private LogFormatter $formatter;

    /**
     * Creates logger instance.
     *
     * @param LogFormatter $formatter Class responsible in creating and formatting logging message.
     */
    public function __construct(LogFormatter $formatter)
    {
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
        error_log($this->formatter->format($info, $level));
    }
}
