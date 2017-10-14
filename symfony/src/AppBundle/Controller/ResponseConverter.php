<?php
namespace AppBundle\Controller;

use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Psr\Http\Message\ResponseInterface;

class ResponseConverter
{
    public static function fromPsr7(ResponseInterface $response)
    {
        return (new HttpFoundationFactory)->createResponse($response);
    }
}