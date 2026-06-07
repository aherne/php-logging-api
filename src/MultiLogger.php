<?php

namespace Lucinda\Logging;

/**
 * Implements a logger that forwards internally to multiple loggers.
 */
final class MultiLogger extends Logger
{
    /**
     * @var Logger[]
     */
    private array $loggers;

    /**
     * Creates an object.
     *
     * @param \Lucinda\Logging\Logger[] $loggers List of loggers to delegate logging to.
     */
    public function __construct(array $loggers)
    {
        $this->loggers = $loggers;
    }

    /**
     * Performs the act of logging.
     *
     * @param string|\Throwable $info  Information that needs being logged
     * @param integer           $level Log level (see: https://tools.ietf.org/html/rfc5424)
     */
    protected function log(string|\Throwable $info, int $level): void
    {
        foreach ($this->loggers as $logger) {
            $logger->log($info, $level);
        }
    }
}
