<?php

namespace Lucinda\Logging\Driver\File;

use Lucinda\Logging\ConfigurationException;
use Lucinda\Logging\LogFormatter;

/**
 * Logs message into file on disk, whose location varies according to development environment.
 */
final class Wrapper extends \Lucinda\Logging\AbstractLoggerWrapper
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
        $filePath = (string) $xml["path"];
        if (!$filePath) {
            throw new ConfigurationException("Attribute 'path' is mandatory for file logger");
        }

        $pattern= (string) $xml["format"];
        if (!$pattern) {
            throw new ConfigurationException("Attribute 'format' is mandatory for file logger");
        }

        return new Logger($filePath, new LogFormatter($pattern, $this->requestInformation), (string) $xml["rotation"]);
    }
}
