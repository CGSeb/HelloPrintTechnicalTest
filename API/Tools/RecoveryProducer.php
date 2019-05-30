<?php
namespace API\Tools;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RecoveryProducer extends AbstractQueue {

    /**
     * @param string $queueName
     */
    public function __construct()
    {
        parent::__construct();
        $this->initChannel();
    }

    private function initChannel()
    {
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare(
            $queue = self::RABBITMQ_RECOVERY_QUEUE,
            $passive = false,
            $durable = true,
            $exclusive = false,
            $auto_delete = false,
            $nowait = false,
            $arguments = null,
            $ticket = null
        );
    }

    /**
     * @param AMQPMessage $message
     */
    public function publish(AMQPMessage $message)
    {
        $this->channel->basic_publish($message, '', self::RABBITMQ_RECOVERY_QUEUE);
    }
}
