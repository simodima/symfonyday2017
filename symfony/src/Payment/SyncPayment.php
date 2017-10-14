<?php
namespace Payment;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use GuzzleHttp\Psr7\Request;

final class SyncPayment implements PaymentInterface
{
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;    
    }

    public function pay(array $data): ResponseInterface
    {
        $response = $this->client->send(
            new Request('POST', '/pay', [], json_encode($data))
        );

        return $response;
    }
}