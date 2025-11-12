<?php

namespace App\Console\Commands;

use Elasticsearch\Client;
use Illuminate\Console\Command;
use Mockery\Exception;

class ElasticsearchPing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elasticsearch:ping';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ping Elasticsearch';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param Client|null $client
     */
    public function handle(?Client $client)
    {
        try {
            if ($client && $client->ping()) {
                if ($client->ping()) {
                    $this->info('pong');
                    return;
                }
            } else {
                $this->error('Could not connect to Elasticsearch.');
            }
        } catch (Exception $ex) {
            $this->error('Could not connect to Elasticsearch.');
        }
    }
}
