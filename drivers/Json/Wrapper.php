<?php

namespace Lucinda\Logging\Driver\Json;

use Lucinda\Logging\ConfigurationException;

/**
 * Creates a logger that writes newline-delimited JSON objects to disk.
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
            throw new ConfigurationException("Attribute 'path' is mandatory for json logger");
        }

        return new Logger($filePath, $this->requestInformation, (string) $xml["rotation"]);
    }
}
