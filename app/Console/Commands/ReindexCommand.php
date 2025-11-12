<?php

namespace App\Console\Commands;

use App\Models\Word;
use Elasticsearch\Client;
use Illuminate\Console\Command;

class ReindexCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elastic:reindex';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Indexes all words to Elasticsearch';

    /** @var Client|null */
    private ?Client $client;

    public function __construct(?Client $client)
    {
        parent::__construct();

        $this->client = $client;
    }

    /**
     * @return void
     */
    public function handle()
    {
        if ($this->client && $this->client->ping()) {
            $count = Word::query()->count();
            $this->info("Indexing {$count} records. This might take a while...");
            if ($this->client->indices()->exists(['index' => 'words'])) {
                $this->client->indices()->delete(['index' => 'words']);
            }
            foreach (Word::query()->get() as $word) {
                $this->client->index([
                    'index' => Word::getElasticIndexName(),
                    'id' => $word->id,
                    'body' => $word->toSearchableArray(),
                ]);
                $this->output->write('.');
            }

            $this->info("\nDone!");
        } else {
            $this->error('Could not connect to Elasticsearch.');
        }
    }
}
