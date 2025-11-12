<?php

namespace App\Http\Traits;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Config;

trait MySearchable
{
    /** @var Client */
    private static $elastic_client;

    /**
     * Get model index in Elasticsearch
     *
     * @return string
     */
    abstract public static function getElasticIndexName(): string;

    public static function bootSearchable()
    {
        if (env('ELASTICSEARCH_ENABLED')) {
            /** Subscribe to created event and add new document to Elasticsearch */
            static::created(function ($model) {
                static::getElasticClient()->index([
                    'index' => static::getElasticIndexName(),
                    'id' => $model->id,
                    'body' => $model->toSearchableArray(),
                ]);
            });

            /** Subscribe to update event and update document to Elasticsearch */
            static::updated(function ($model) {
                static::getElasticClient()->update([
                    'index' => static::getElasticIndexName(),
                    'id' => $model->id,
                    'body' => [
                        'doc' => $model->toSearchableArray(),
                    ],
                ]);
            });

            /** Subscribe to delete event and delete document from Elasticsearch */
            static::deleted(function ($model) {
                static::getElasticClient()->delete([
                    'index' => static::getElasticIndexName(),
                    'id' => $model->id,
                ]);
            });
        }
    }

    /**
     * Get Elasticsearch Client
     *
     * @return Client|null
     */
    private static function getElasticClient(): Client|null
    {
        if (!env('ELASTICSEARCH_ENABLED')) {
            return null;
        }
        if (!static::$elastic_client) {
            return static::$elastic_client = ClientBuilder::fromConfig(
                Config::get('elasticsearch')
            );
        }

        return static::$elastic_client;
    }
}
