<?php

namespace Test\Lucinda\Logging;

use Lucinda\Logging\RequestInformation;
use Lucinda\UnitTest\Result;

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
        return new Result("tested via getUri() method");
    }


    public function setIpAddress()
    {
        $this->requestInformation->setIpAddress("127.0.0.1");
        return new Result("tested via getIpAddress() method");
    }


    public function setUserAgent()
    {
        $this->requestInformation->setUserAgent("chrome");
        return new Result("tested via getUserAgent() method");
    }


    public function getUri()
    {
        return new Result($this->requestInformation->getUri()=="test");
    }


    public function getIpAddress()
    {
        return new Result($this->requestInformation->getIpAddress()=="127.0.0.1");
    }


    public function getUserAgent()
    {
        return new Result($this->requestInformation->getUserAgent()=="chrome");
    }
}
