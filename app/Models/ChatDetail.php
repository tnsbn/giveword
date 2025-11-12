<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'body',
        'sender_id',
        'receiver_id',
        'chat_queue_id',
        'read_at',
        'receiver_deleted_at',
        'sender_deleted_at',
    ];

    protected $dates = ['read_at','receiver_deleted_at','sender_deleted_at'];

    /**
     * @return BelongsTo
     */
    public function chatQueue(): BelongsTo
    {
        return $this->belongsTo(ChatQueue::class);
    }

    public static function addDetail(array $detail): array
    {
        try {
            $chatQueueId = ChatQueue::query()
                ->where([
                'sender_id' => $detail['sender'],
                'receiver_id' => $detail['receiver_id'],
            ])->get();
            if (!empty($chatQueueId)) {
                $chatDetail = new ChatDetail([
                    'sender_id' => $detail['sender'],
                    'receiver_id' => $detail['receiver'],
                    'body' => $detail['body'],
                    'chat_queue_id' => $chatQueueId[0]->id,
                ]);
                $chatDetail->saveOrFail();
                return ['msg' => ''];
            } else {
                return ['error' => 'Server error'];
            }
        } catch (\Throwable $ex) {
            return ['error' => 'Server error'];
        }
    }

    public function isRead(): bool
    {
         return $this->read_at != null;
    }
}
