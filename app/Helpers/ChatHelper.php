<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Redis;

class ChatHelper
{
    /**
     * @return false|mixed|string
     */
    public static function isOnline(): bool
    {
        return true;
        $redis = Redis::connection()->client();
        return $redis->exists('chat_status') && $redis->get('chat_status') == 'start';
    }

    /**
     * @return void
     */
    public static function getPartnerName()
    {
        $redis = Redis::connection()->client();
        return User::isAdmin() ?
            $redis->get('chat_guest_name') :
            User::getMainChatUser();
    }
}
