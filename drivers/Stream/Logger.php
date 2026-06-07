<?php

namespace Lucinda\Logging\Driver\Stream;

use Lucinda\Logging\ConfigurationException;
use Lucinda\Logging\LogFormatter;

/**
 * Logs messages/errors into stdout or stderr.
 */
class Logger extends \Lucinda\Logging\Logger
{
    private string $stream;
    private LogFormatter $formatter;

    /**
     * Creates logger instance.
     *
     * @param string       $stream    Stream URI to write into.
     * @param LogFormatter $formatter Class responsible in creating and formatting logging message.
     * @throws ConfigurationException If stream is not supported.
     */
    public function __construct(string $stream, LogFormatter $formatter)
    {
        if (!in_array($stream, ["php://stdout", "php://stderr"], true)) {
            throw new ConfigurationException("Stream logger only supports 'php://stdout' or 'php://stderr'");
        }

        $this->stream = $stream;
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
        file_put_contents($this->stream, $this->formatter->format($info, $level)."\n");
    }
}
