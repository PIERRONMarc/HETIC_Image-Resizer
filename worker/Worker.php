<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

class Worker 
{
    private const MB_ADDRESS = 'rabbitmq';
    private const MB_PORT = 5672;
    private const MB_USER = 'root';
    private const MB_PASSWORD = 'root';

    private $channel;

    private $connection;

    /**
     * Start the worker and consume the 'image-resizing' queue
     */
    public function run()
    {
        $this->connect();
        $this->channel->queue_declare('image-resizing', false, true, false, false);

        echo " [*] Waiting for messages. To exit press CTRL+C\n";

        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume('image-resizing', '', false, false, false, false, [Worker::class, 'action']);

        while ($this->channel->is_open()) {
            $this->channel->wait();
        }

        $this->close();
    }

    /**
     * Connect the worker to the message broker
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
     * The action effectued by the worker for a given message
     */
    public function action($msg) 
    {
        $json = json_decode($msg->body, true);
        echo ' [x] Received : ', implode(';', $json), "\n";

        // .. insert code here ..

        // confirm that the task is done
        $msg->ack();
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