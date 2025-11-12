<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatQueue extends Model
{
    use HasFactory;

    protected $fillable = [
        'receiver_id',
        'sender_id'
    ];

    /**
     * @return HasMany
     */
    public function chatDetail(): HasMany
    {
        return $this->hasMany(ChatDetail::class);
    }

    public function getReceiver()
    {
        if ($this->sender_id === auth()->id()) {
            return User::query()->firstWhere('id', $this->receiver_id);
        } else {
            return User::query()->firstWhere('id', $this->sender_id);
        }
    }

    public function scopeWhereNotDeleted($query)
    {
        $userId = auth()->id();

        return $query->where(function ($query) use ($userId) {

            #where message is not deleted
            $query->whereHas('messages', function ($query) use ($userId) {

                $query->where(function ($query) use ($userId) {
                    $query->where('sender_id', $userId)
                        ->whereNull('sender_deleted_at');
                })->orWhere(function ($query) use ($userId) {

                    $query->where('receiver_id', $userId)
                    ->whereNull('receiver_deleted_at');
                });
            })
             #include conversations without messages
              ->orWhereDoesntHave('messages');
        });
    }



    public function isLastMessageReadByUser(): bool
    {


        $user = Auth()->User();
        $lastMessage = $this->messages()->latest()->first();

        if ($lastMessage) {
            return  $lastMessage->read_at !== null && $lastMessage->sender_id == $user->id;
        }
    }




    public function unreadMessagesCount(): int
    {
        return $unreadMessages = ChatDetail::query()
            ->where('chat_queue_id', '=', $this->id)
            ->where('receiver_id', auth()->user()->id)
            ->whereNull('read_at')->count();
    }
}
