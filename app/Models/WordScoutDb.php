<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Scout\EngineManager;
use Laravel\Scout\Engines\Engine;
use Laravel\Scout\Searchable;

class WordScoutDb extends Model
{
    use Notifiable;
    use SoftDeletes;
    use Searchable;

    protected $table = 'words';
    public $timestamps = true;
    protected $fillable = [
        'user_id', 'message', 'price', 'tags',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * @return HasMany
     */
    public function userTookWord(): HasMany
    {
        return $this->hasMany(
            'App\Models\UserTookWord',
            localKey: 'word_id',
        );
    }

    /**
     * Get the engine used to index the model.
     */
    public function searchableUsing(): Engine
    {
        return app(EngineManager::class)->engine('database');
    }

    /**
     * For Scout.
     *
     * @return string
     */
    public function searchableAs(): string
    {
        return 'words_index';
    }

    /**
     * For Scout.
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
//    #[SearchUsingPrefix(['tags'])]
//    #[SearchUsingPrefix(['message', 'tags'])]
//    #[SearchUsingFullText(['message', 'tags'])]
    public function toSearchableArray(): array
    {
        return [
            'message' => $this->message,
            'tags' => $this->tags,
        ];
    }
}
