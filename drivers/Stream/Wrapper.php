<?php

namespace Lucinda\Logging\Driver\Stream;

use Lucinda\Logging\ConfigurationException;
use Lucinda\Logging\LogFormatter;

/**
 * Creates a logger that writes to stdout or stderr.
 */
class Wrapper extends \Lucinda\Logging\AbstractLoggerWrapper
{
    /**
     * Detects Logger instance based on XML tag supplied
     *
     * @param  \SimpleXMLElement $xml XML tag that is child of loggers.(environment)
     * @return Logger
     * @throws ConfigurationException If resources referenced in XML do not exist or do not extend/implement required blueprint.
     */
    protected function setLogger(\SimpleXMLElement $xml): \Lucinda\Logging\Logger
    {
        $stream = (string) $xml["stream"];
        if (!$stream) {
            throw new ConfigurationException("Attribute 'stream' is mandatory for stream logger");
        }

        $pattern = (string) $xml["format"];
        if (!$pattern) {
            throw new ConfigurationException("Attribute 'format' is mandatory for stream logger");
        }

        return new Logger($stream, new LogFormatter($pattern, $this->requestInformation));
    }
}
