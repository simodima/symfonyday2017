<?php

namespace Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Psr\Http\Message\ResponseInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Notification\WSNotifier;
use Payment\SyncPayment;

class PaymentConsumer implements ConsumerInterface
{
    const WAIT = 3 * 1000;

    private $payment;
    private $notifier;
    private $delayer;

    public function __construct(SyncPayment $payment, WSNotifier $notifier, ProducerInterface $delayer, $logger)
    {
        $this->payment  = $payment;
        $this->notifier = $notifier;
        $this->delayer  = $delayer;
    }

    public function execute(AMQPMessage $msg)
    {
        if ($msg->delivery_info['redelivered']) {
            return $this->handleRedeliveredMessage($msg);
        }
        
        $response = $this->payment->pay(json_decode($msg->body, true));
        
        switch (true) {
            case $response->getStatusCode() == 200:
                return $this->handleSuccessfulPayment($msg, $response);
            default:
                return $this->handleFailedPayment($msg);     
        }
    }

    private function handleRedeliveredMessage(AMQPMessage $msg): int
    {
        $data          = json_decode($msg->body, true);
        $data['delay'] = ($data['delay'] ?? 0) + self::WAIT;
        
        $this->notifier->notify(
            $data['channel'],
            json_encode(['status' => sprintf('Rate limit detected, wait %s msec...', $data['delay'])])
        );

        $this->delayer->publish(
            json_encode($data), 
            '', 
            ['expiration' => $data['delay']]
        );

        return ConsumerInterface::MSG_REJECT;
    }

    private function handleSuccessfulPayment(AMQPMessage $msg, ResponseInterface $response): int
    {
        $data    = json_decode($msg->body, true);
        $channel = $data['channel'];
        $this->notifier->notify($channel, $response->getBody());
        
        return ConsumerInterface::MSG_ACK;
    }

    private function handleFailedPayment(AMQPMessage $msg): int
    {
        $data    = json_decode($msg->body, true);
        $channel = $data['channel'];
        $this->notifier->notify($channel, json_encode(['status' => 'Payment failed, retrying...']));
        
        return ConsumerInterface::MSG_REJECT_REQUEUE;
    }
}