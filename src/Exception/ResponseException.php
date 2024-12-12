<?php

namespace App\Exception;

use Exception;
use Throwable;
use Symfony\Component\HttpFoundation\Response;

class ResponseException extends Exception
{
    /** @var Response */
    private $response;

    public function __construct(Response $response)
    {
        parent::__construct("Response exception");

        $this->response = $response;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}
