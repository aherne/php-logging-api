<?php

namespace Lucinda\Logging\Driver\SysLog;

use Lucinda\Logging\ConfigurationException;
use Lucinda\Logging\LogFormatter;

/**
 * Logs message into a dedicated SYSLOG server, whose details may vary according to development environment.
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
        $applicationName = (string) $xml["application"];
        if (!$applicationName) {
            throw new ConfigurationException("Attribute 'application' is mandatory for sys logger");
        }

        $pattern= (string) $xml["format"];
        if (!$pattern) {
            throw new ConfigurationException("Attribute 'format' is mandatory for sys logger");
        }

        return new Logger($applicationName, new LogFormatter($pattern, $this->requestInformation));
    }
}
