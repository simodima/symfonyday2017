<?php

namespace Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Notification\WSNotifier;
use Payment\SyncPayment;

class PaymentConsumer implements ConsumerInterface
{
    private $payment;
    private $notifier;

    public function __construct(SyncPayment $payment, WSNotifier $notifier)
    {
        $this->payment  = $payment;
        $this->notifier = $notifier;
    }

    public function execute(AMQPMessage $msg)
    {
        $data     = json_decode($msg->body, true);
        $response = $this->payment->pay($data);

        if ($response->getStatusCode() == 200) {
            $this->notifier->notify($data['channel'], $response->getBody());
            return ConsumerInterface::MSG_ACK;
        }

        $this->notifier->notify($data['channel'], json_encode(['status' => 'Payment failed, retrying...']));
        
        return ConsumerInterface::MSG_REJECT_REQUEUE;
    }
}