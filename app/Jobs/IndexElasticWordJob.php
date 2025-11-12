<?php

namespace App\Jobs;

use App\Models\Word;
use Elasticsearch\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IndexElasticWordJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected Word $word;

    /**
     * Create a new job instance.
     */
    public function __construct(Word $word)
    {
        $this->word = $word;
    }

    /**
     * Execute the job.
     *
     * @param Client|null $client
     * @return void
     */
    public function handle(?Client $client): void
    {
        if ($client && $client->ping()) {
            try {
                $params = [
                    'index' => Word::getElasticIndexName(),
                    'id' => $this->word->id,
                    'body' => $this->word->toArray(),
                ];
                $client->index($params);
            } catch (\Throwable $ex) {
                dump($ex->getMessage());
            }
        }
    }
}
