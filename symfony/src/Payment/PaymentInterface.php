<?php
namespace Payment;

use Psr\Http\Message\ResponseInterface;

interface PaymentInterface 
{
    public function Pay(array $data): ResponseInterface;
}