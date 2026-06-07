<?php

namespace Test\Lucinda\Logging;

use Lucinda\Logging\RequestInformation;
use Lucinda\UnitTest\Validator\Booleans;
use Lucinda\UnitTest\Validator\Strings;

class RequestInformationTest
{
    private RequestInformation $requestInformation;

    public function __construct()
    {
        $this->requestInformation = new RequestInformation();
    }

    public function setUri()
    {
        $this->requestInformation->setUri("test");
        return (new Strings($this->requestInformation->getUri()))->assertEquals("test");
    }

    public function setIpAddress()
    {
        $this->requestInformation->setIpAddress("127.0.0.1");
        return (new Strings($this->requestInformation->getIpAddress()))->assertEquals("127.0.0.1");
    }

    public function setUserAgent()
    {
        $this->requestInformation->setUserAgent("chrome");
        return (new Strings($this->requestInformation->getUserAgent()))->assertEquals("chrome");
    }

    public function getUri()
    {
        return (new Strings($this->requestInformation->getUri()))->assertEquals("test");
    }

    public function getIpAddress()
    {
        return (new Strings($this->requestInformation->getIpAddress()))->assertEquals("127.0.0.1");
    }

    public function getUserAgent()
    {
        return (new Strings($this->requestInformation->getUserAgent()))->assertEquals("chrome");
    }

    public function defaults()
    {
        $requestInformation = new RequestInformation();

        return [
            (new Booleans($requestInformation->getUri() === null))->assertTrue("uri defaults to null"),
            (new Booleans($requestInformation->getIpAddress() === null))->assertTrue("ip address defaults to null"),
            (new Booleans($requestInformation->getUserAgent() === null))->assertTrue("user agent defaults to null")
        ];
    }
}
