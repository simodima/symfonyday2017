<?php
namespace Payment;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\JsonResponse;

final class AsyncPayment implements PaymentInterface
{
    private $producer;

    public function __construct(ProducerInterface $producer)
    {
        $this->producer = $producer;    
    }

    public function pay(array $data): ResponseInterface
    {
        $this->producer->publish(json_encode($data));
        
        return new JsonResponse($data, 201);
    }
}