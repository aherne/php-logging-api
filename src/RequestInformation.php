<?php

namespace Lucinda\Logging;

/**
 * Encapsulates user request information
 */
class RequestInformation
{
    private ?string $uri = null;
    private ?string $ipAddress = null;
    private ?string $userAgent = null;

    /**
     * Sets relative URI (page) requested by client
     *
     * @param string $uri
     */
    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }

    /**
     * Sets ip address used by client
     *
     * @param string $ipAddress
     */
    public function setIpAddress(string $ipAddress): void
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * Sets client's user agent
     *
     * @param string $userAgent
     */
    public function setUserAgent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }

    /**
     * Gets relative URI (page) requested by client
     *
     * @return ?string
     */
    public function getUri(): ?string
    {
        return $this->uri;
    }

    /**
     * Gets ip address used by client
     *
     * @return ?string
     */
    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    /**
     * Gets client's user agent
     *
     * @return ?string
     */
    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }
}
