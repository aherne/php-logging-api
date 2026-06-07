<?php

namespace Lucinda\Logging\Driver\File;

use Lucinda\Logging\LogFormatter;

/**
 * Logs messages/errors into simple files.
 */
class Logger extends \Lucinda\Logging\Logger
{
    public const EXTENSION = "log";

    private string $filePath;
    private string $rotationPattern;
    private LogFormatter $formatter;

    /**
     * Creates logger instance.
     *
     * @param string       $filePath        Log file (without extension) and its absolute path.
     * @param LogFormatter $formatter       Class responsible in creating and formatting logging message.
     * @param string       $rotationPattern PHP date function format by which logs will rotate.
     */
    public function __construct(string $filePath, LogFormatter $formatter, string $rotationPattern="")
    {
        $this->filePath = $filePath;
        $this->rotationPattern = $rotationPattern;
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
        $fileName = $this->filePath.($this->rotationPattern ? "__".date($this->rotationPattern) : "").".".self::EXTENSION;
        file_put_contents($fileName, $this->formatter->format($info, $level)."\n", FILE_APPEND | LOCK_EX);
    }
}
