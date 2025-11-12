<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

//use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

//    use HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return HasMany
     */
    public function userTookWord(): HasMany
    {
        return $this->hasMany(UserTookWord::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * @return bool
     */
    public static function isAdmin(): bool
    {
        return auth()->user() !== null and auth()->user()->id == env('CHAT_UID');
    }

    /**
     * @return string
     */
    public static function getMainChatUser(): string
    {
        $user = User::query()->where('id', '=', env('CHAT_UID') ?? 2)
            ->select(['name'])
            ->first()
            ->toArray();
        return $user['name'] ?? '';
    }

    public function chatQueue(): HasMany
    {
        return $this->hasMany(ChatQueue::class, 'sender_id')
            ->orWhere('receiver_id', $this->id);
//            ->whereNotDeleted();
    }

    /**
     * The channels the user receives notification broadcasts on.
     */
    public function receivesBroadcastNotificationsOn(): string
    {
        return 'users.' . $this->id;
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function getByEmail(string $email): ?User
    {
        $users = User::query()
            ->where('email', '=', $email)
            ->get();
        return $users[0] ?? null;
    }

    public static function userOnlineStatus()
    {
        $users = User::all();
        foreach ($users as $user) {
            if (Cache::has('user-is-online-' . $user->id)) {
                echo $user->name . " is online-------. Last seen: " . Carbon::parse($user->last_seen)->diffForHumans();
            } else {
                echo $user->name . " is offline. Last seen: " . Carbon::parse($user->last_seen)->diffForHumans();
            }
            echo "<br>";
        }
    }
}
