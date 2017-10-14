<?php

namespace Notification;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;

class WSNotifier 
{
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function notify(string $channel, string $message)
    {
        $request = new Request(
            'POST', 
            sprintf('/notify/%s', $channel),
            [],
            $message
        );

        $this->client->send($request);
    }
}