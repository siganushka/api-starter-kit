<?php

namespace App\View;

use Symfony\Component\HttpFoundation\Response;

class View implements ViewInterface
{
    private $data;
    private $httpStatusCode;

    public function __construct($data, int $httpStatusCode = Response::HTTP_OK)
    {
        $this->data = $data;
        $this->httpStatusCode = $httpStatusCode;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }

    public function setHttpStatusCode(int $httpStatusCode)
    {
        $this->httpStatusCode = $httpStatusCode;

        return $this;
    }
}
