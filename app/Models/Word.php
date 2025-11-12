<?php

namespace App\Models;

use App\Constants\AppConst;
use App\Http\Components\DynamoConnector;
use App\Http\Traits\MySearchable;
use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use JeroenG\Explorer\Application\Explored;
use JetBrains\PhpStorm\ArrayShape;
use Laravel\Scout\Searchable;

class Word extends Model implements Explored
{
    use Notifiable;
    use SoftDeletes;
    use HasFactory;
    use MySearchable, Searchable {
        MySearchable::bootSearchable insteadof Searchable;
    }

    protected bool $softDelete = true;
    protected $table = 'words';
    public $timestamps = true;
    protected $fillable = [
        'user_id', 'message', 'price', 'tags', 'deleted_at',
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
        return $this->hasMany('App\Models\UserTookWord');
    }

    /**
     * @return array
     */
    public static function getTags(): array
    {
        // Get recent tags
        $limitTags = [];
        $recentTags = Word::query()
            ->whereNotNull('tags')
            ->select(['tags', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->limit(AppConst::RECENT_TAGS_LIMIT)
            ->get()
            ->toArray();
        foreach ($recentTags as $tag) {
            $limitTags = array_merge(
                array_values($limitTags),
                array_values(tagsToArray($tag['tags']))
            );
        }

        $limitTags = tagsToArray($limitTags);

        return array_slice($limitTags, 0, AppConst::RECENT_TAGS_LIMIT);
    }

    /**
     * @return void
     * @throws Exception
     */
    public static function cacheTags(): void
    {
        if (DynamoConnector::onCloud()) {
            $dynamo = DynamoConnector::getDnm();
//            if ($dynamo->getCredentials()->getState() !== PromiseInterface::FULFILLED) {
            $limitTags = self::getTags();
            $limitTags = implode(',', $limitTags);
            $result = $dynamo->getItem(
                [
                'ConsistentRead' => true,
                'TableName' => 'cache',
                'Key' => [
                    'ore' => ['S' => 'tags'],
                ],
                ]
            );
            if (empty($result['Item'])) {
                $dynamo->putItem(
                    [
                    'TableName' => 'cache',
                    'Item' => [
                        'ore' => ['S' => 'tags'],
                        'value' => ['S' => $limitTags],
                    ],
                    ]
                );
            } else {
                $dynamo->updateItem(
                    [
                    'TableName' => 'cache',
                    'Key' => [
                        'ore' => ['S' => 'tags'],
                    ],
                    'AttributeUpdates' => [
                        'value' => [
                            'Value' => ['S' => $limitTags]
                        ],
                    ],
                    ]
                );
            }
//            }
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    public static function getCachedTags(): array
    {
        if (DynamoConnector::onCloud()) {
            $dynamo = DynamoConnector::getDnm();
            if ($dynamo->getCredentials()->getState() == PromiseInterface::FULFILLED) {
                $result = $dynamo->getItem(
                    [
                    'ConsistentRead' => true,
                    'TableName' => 'cache',
                    'Key' => [
                        'ore' => ['S' => 'tags'],
                    ],
                    ]
                );
                if ($result['Item']) {
                    return explode(',', $result['Item']['value']['S']);
                }
            }
        }

        return self::getTags();
    }

    /**
     * @param  $words
     * @return mixed
     */
    public static function addPaginateDetail($words): mixed
    {
        $result = $words;
        array_map(
            function ($word) {
                $word['tags'] = tagsToArray($word['tags']);
                $word['short_date']
                    = date_create($word['created_at'])->format('M, d Y');
                $word['username'] = $word->user()->first()['name'] ?? "";
                $userIds = $word['userTookWord']->pluck('user_id')->toArray();
                $word['taken_count'] = count(array_unique($userIds));
                $word['already_taken']
                    = Auth::user() && in_array(Auth::user()->id, $userIds);
                $word['can_take_this'] = !Auth::user() ||  (
                    Auth::user()->id != $word['user_id']
                    && !in_array(Auth::user()->id, $userIds)
                );
                return $word;
            },
            $result->items()
        );

        return $result;
    }

    /**
     * @return string
     */
    public function searchableAs(): string
    {
        return 'words';
    }

    /**
     * For Elastic.
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return $this->toArray();
    }

    /**
     * @return string[]
     */
    #[ArrayShape(['id' => "string", 'message' => "string", 'tags' => "string"])]
    public function mappableAs(): array
    {
        return [
            'id' => 'keyword',
            'message' => 'text',
            'tags' => 'text',
        ];
    }

    /**
     * @return string
     */
    public static function getElasticIndexName(): string
    {
        return 'words';
    }

    /**
     * Search document by query.
     *
     * @param string $keyword
     * @return array
     */
    public static function consoleSearch(array $query): array
    {
//        $searchParams = [
//            'query' => [
//                'match' => [
//                    'message' => $keyword,
//                ],
//            ],
//        ];
        return static::getElasticClient()->search([
            'index' => static::getElasticIndexName(),
            'body'  => $query,
        ]);
    }

    /**
     * @param array $query
     * @return int
     */
    public static function countSearch(array $query): int
    {
        $search = static::getElasticClient()->search([
            'index' => static::getElasticIndexName(),
            'body'  => $query,
        ]);
        return count($search);
    }
}
