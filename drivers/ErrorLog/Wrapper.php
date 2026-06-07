<?php

namespace Lucinda\Logging\Driver\ErrorLog;

use Lucinda\Logging\ConfigurationException;
use Lucinda\Logging\LogFormatter;

/**
 * Creates a logger that delegates to PHP's native error_log facility.
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
        $pattern = (string) $xml["format"];
        if (!$pattern) {
            throw new ConfigurationException("Attribute 'format' is mandatory for error log logger");
        }

        return new Logger(new LogFormatter($pattern, $this->requestInformation));
    }
}
