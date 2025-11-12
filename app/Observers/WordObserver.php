<?php

namespace App\Observers;

use App\Jobs\IndexElasticWordJob;
use App\Jobs\RemoveElasticWordJob;
use App\Models\Word;

class WordObserver
{
    /**
     * Handle the Word "created" event.
     *
     * @param Word $word
     * @return void
     */
    public function created(Word $word)
    {
        if (env('ELASTICSEARCH_ENABLED')) {
            dispatch(new IndexElasticWordJob($word));
        }
    }

    /**
     * Handle the Word "updated" event.
     *
     * @param Word $word
     * @return void
     */
    public function updated(Word $word)
    {
        if (env('ELASTICSEARCH_ENABLED')) {
            dispatch(new IndexElasticWordJob($word));
        }
    }

    /**
     * Handle the Word "deleted" event.
     *
     * @param Word $word
     * @return void
     */
    public function deleted(Word $word)
    {
        if (env('ELASTICSEARCH_ENABLED')) {
            dispatch(new RemoveElasticWordJob($word));
        }
    }

    /**
     * Handle the Word "restored" event.
     *
     * @param Word $word
     * @return void
     */
    public function restored(Word $word)
    {
        if (env('ELASTICSEARCH_ENABLED')) {
            dispatch(new IndexElasticWordJob($word));
        }
    }

    /**
     * Handle the Word "force deleted" event.
     *
     * @param Word $word
     * @return void
     */
    public function forceDeleted(Word $word)
    {
        if (env('ELASTICSEARCH_ENABLED')) {
            dispatch(new RemoveElasticWordJob($word));
        }
    }
}
