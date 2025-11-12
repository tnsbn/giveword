<?php

namespace App\Jobs;

use App\Models\Word;
use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveElasticWordJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected Word $word;

    public function __construct($word)
    {
        $this->word = $word;
    }

    /**
     * @param Client|null $client
     * @return void
     */
    public function handle(?Client $client): void
    {
        if (env('ELASTICSEARCH_ENABLED')) {
            try {
                $params = [
                    'index' => Word::getElasticIndexName(),
                    'id' => $this->word->id,
                ];

                $client->delete($params);
            } catch (Missing404Exception $exception) {
                // Already deleted
            }
        }
    }
}
