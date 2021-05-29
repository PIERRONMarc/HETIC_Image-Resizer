<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class MessageBroker 
{
    private const MB_ADDRESS = 'rabbitmq';
    private const MB_PORT = 5672;
    private const MB_USER = 'root';
    private const MB_PASSWORD = 'root';

    private $channel;

    private $connection;

    /**
     * Send a message to the 'image-resizing' queue
     *
     * @param String $data the data of the message
     * @return void
     */
    public function sendMessage(String $data)
    {
        $this->connect();
        $this->channel->queue_declare('image-resizing', false, true, false, false);

        $msg = new AMQPMessage($data, [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
        ]);

        $this->channel->basic_publish($msg, '', 'image-resizing');

        echo " [x] Sent : " . implode(';', json_decode($data, true)). "\n";

        $this->close();
    }

    /**
     * Connect to the message broker
     */
    private function connect()
    {
        $this->connection = new AMQPStreamConnection(
            $this::MB_ADDRESS,
            $this::MB_PORT,
            $this::MB_USER,
            $this::MB_PASSWORD
        );
        $this->channel = $this->connection->channel();
    }

    /**
     * Close the connection with the message broker
     */
    private function close()
    {
        $this->channel->close();
        $this->connection->close();
    }

}