<?php
namespace API\Tools;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

abstract class AbstractQueue
{
    const RABBITMQ_HOST             = 'helloprint-core-rabbit';
    const RABBITMQ_PORT             = 5672;
    const RABBITMQ_USERNAME         = 'rabbitmq';
    const RABBITMQ_PASSWORD         = 'rabbitmq';
    const RABBITMQ_LOGIN_QUEUE      = 'helloprint-login';
    const RABBITMQ_RECOVERY_QUEUE   = 'helloprint-recovery';

    /**
     * @var AMQPStreamConnection
     */
    protected $connection;

    /**
     * @var AMQPChannel
     */
    protected $channel;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            self::RABBITMQ_HOST,
            self::RABBITMQ_PORT,
            self::RABBITMQ_USERNAME,
            self::RABBITMQ_PASSWORD
        );
    }

    public function closeQueue()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
