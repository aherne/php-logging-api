<?php
namespace Lucinda\Logging;

/**
 * Locates and instances loggers based on XML content.
 */
class Wrapper
{
    private array $loggers = array();
    
    /**
     * Reads XML tag loggers.{environment}, finds and saves loggers found.
     *
     * @param \SimpleXMLElement $xml XML containing logger settings.
     * @param string $developmentEnvironment Development environment server is running into (eg: local, dev, live)
     * @throws ConfigurationException If pointed file doesn't exist or is invalid
     */
    public function __construct(\SimpleXMLElement $xml, string $developmentEnvironment)
    {
        if (empty($xml->loggers)) {
            return;
        }
        $this->setLoggers($xml->loggers->{$developmentEnvironment});
    }
    
    /**
     * Reads XML tag for loggers and saves them for later use.
     *
     * @param \SimpleXMLElement $xml XML containing individual logger settings.
     * @throws ConfigurationException If pointed file doesn't exist or is invalid
     */
    private function setLoggers(\SimpleXMLElement $xml): void
    {
        $list = $xml->xpath("logger");
        foreach ($list as $xmlProperties) {
            // detects class name
            $className = (string) $xmlProperties["class"];
            if (!$className) {
                throw new ConfigurationException("Attribute 'class' is mandatory for 'logger' tag");
            }

            // detects wrapper for loggers
            $loggerWrapper = new $className($xmlProperties);
            $this->loggers[] = $loggerWrapper->getLogger();
        }
    }
    
    /**
     * Gets detected logger.
     *
     * @return Logger List of loggers found.
     */
    public function getLogger(): Logger
    {
        return new MultiLogger($this->loggers);
    }
}
