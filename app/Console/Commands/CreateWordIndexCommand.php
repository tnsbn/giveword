<?php

namespace App\Console\Commands;

use App\Models\Word;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class CreateWordIndexCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elastic:create-index';

    /**
     * @var Client
     */
    private Client $client;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ClientBuilder $builder)
    {
        parent::__construct();

        if (env('ELASTICSEARCH_ENABLED')) {
            $this->client = $builder::fromConfig(
                Config::get('elasticsearch')
            );
        }
    }

    /**
     * @return void
     */
    public function handle()
    {
        if (env('ELASTICSEARCH_ENABLED')) {
            $params = [
                'index' => Word::getElasticIndexName(),
                'body' => [
                    'mappings' => [
                        'properties' => [
                            'id' => [
                                'type' => 'integer',
                            ],
                            'user_id' => [
                                'type' => 'integer',
                            ],
                            'tags' => [
                                'type' => 'text',
                            ],
                            'message' => [
                                'type' => 'text',
                            ],
                            'price' => [
                                'type' => 'integer',
                            ],
                            'created_at' => [
                                'type' => 'date',
                            ],
                            'updated_at' => [
                                'type' => 'date',
                            ],
                            'deleted_at' => [
                                'type' => 'date',
                            ],
                        ],
                    ],
                ],
            ];

            if ($this->client->indices()->exists(['index' => 'words'])) {
                $this->client->indices()->delete(['index' => 'words']);
            }
            $this->client->indices()->create($params);
            $this->info("\nDone!");
        } else {
            $this->error('Could not connect to Elasticsearch.');
        }
    }
}
