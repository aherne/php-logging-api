<?php

namespace Lucinda\Logging\Driver\Null;

/**
 * Silently discards all log messages.
 */
final class Logger extends \Lucinda\Logging\Logger
{
    /**
     * Performs the act of logging.
     *
     * @param string|\Throwable $info  Information that needs being logged
     * @param integer           $level Log level (see: https://tools.ietf.org/html/rfc5424)
     */
    protected function log(string|\Throwable $info, int $level): void
    {
    }
}
