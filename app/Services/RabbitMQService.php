<?php

namespace App\Services;

use App\Models\ChatDetail;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Redis;
use JetBrains\PhpStorm\ArrayShape;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    /**
     * @param  $message
     * @param  $channelUuid
     * @param  $sender
     * @param  $receiver
     * @return array
     * @throws Exception
     */
    #[ArrayShape(['sender' => "int", 'receiver' => "int", 'time' => "string", 'body' => "string"])]
    public function publish($message, $channelUuid, $sender, $receiver): array
    {
        $connection = new AMQPStreamConnection(
            env('MQ_HOST'),
            env('MQ_PORT'),
            env('MQ_USER'),
            env('MQ_PASS')
        );
        $channel = $connection->channel();
        $channel->exchange_declare('suw_exchange', 'direct', false, false, false);
        $channel->queue_declare($channelUuid, false, false, false, false);
        $channel->queue_bind($channelUuid, 'suw_exchange', 'suw_key');
        $msgInfo = $this->addMessageInfo($message, $sender, $receiver);
        $msg = new AMQPMessage($msgInfo);
        $channel->basic_publish($msg, 'suw_exchange', 'suw_key');
        //        echo $message;
        $channel->close();
        $connection->close();
        return $this->parseMessage($msgInfo);
    }

    /**
     * @param  $channelUuid
     * @return void
     * @throws Exception
     */
    public function consume($channelUuid)
    {
        $connection = new AMQPStreamConnection(
            env('MQ_HOST'),
            env('MQ_PORT'),
            env('MQ_USER'),
            env('MQ_PASS')
        );
        $channel = $connection->channel();
        $redis = Redis::connection()->client();
        $callback = function ($msg) use ($redis, $channelUuid) {
            /* @var AMQPMessage $msg */
            $msgInfo = $this->parseMessage($msg->body);
            $userChannel = $channelUuid . '_' . $msgInfo['sender'] . '_' . $msgInfo['receiver'];
            ChatDetail::addDetail($msgInfo);
            $redis->hSet($userChannel, $msgInfo['time'], $msgInfo['body']);
            //            echo $msg->body;
        };
        $channel->queue_declare($channelUuid, false, false, false, false);
        $channel->basic_consume(
            $channelUuid,
            '',
            false,
            true,
            false,
            false,
            $callback
        );
        echo "Waiting for new message on ${channelUuid}", " \n";
        while ($channel->is_consuming()) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }

    /**
     * @param  $msg
     * @param  $sender
     * @param  $receiver
     * @return string
     * @throws Exception
     */
    private function addMessageInfo($msg, $sender, $receiver): string
    {
        $utc = new DateTime('now', new \DateTimeZone('UTC'));
        return "{$sender}:{$receiver}@@{$utc->getTimestamp()}##" . $msg;
    }

    /**
     * @param  string $msg
     * @return array
     */
    #[ArrayShape(['sender' => "int", 'receiver' => "int", 'time' => "string", 'body' => "string"])]
    private function parseMessage(string $msg): array
    {
        $parts = explode('@@', $msg);
        return [
            'sender' => intval(explode(':', $parts[0])[0]),
            'receiver' => intval(explode(':', $parts[0])[1]),
            'time' => explode('##', $parts[1])[0],
            'body' => substr($msg, strpos($msg, '##') + 2),
        ];
    }
}
