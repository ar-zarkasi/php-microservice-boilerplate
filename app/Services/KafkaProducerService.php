<?php
declare(strict_types=1);

namespace App\Services;

use Hyperf\Kafka\Producer;
use longlang\phpkafka\Producer\ProduceMessage;

class KafkaProducerService extends BaseService
{
    public function __construct(protected Producer $client)
    {
    }

    public function sendMessage(string $topic, string $message): void
    {
        $this->client->send($topic, $message);
    }

    public function sendBatchMessage(array $messages): void
    {
        $batchMessages = [];
        foreach ($messages as $topic => $data) {
            $batchMessages[] = new ProduceMessage($topic, $data['message'], $data['key'] ?? null);
        }

        $this->client->sendBatch($batchMessages);
    }
}