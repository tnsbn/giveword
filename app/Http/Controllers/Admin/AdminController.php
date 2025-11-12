<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function Aws\boolean_value;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the dashboard.
     *
     * @return Renderable
     */
    public function index()
    {
        if (!User::isAdmin()) {
            throw new NotFoundHttpException();
        }
        /* @var User $user */
        $user = Auth::user();
        $chatQueues = $user->chatQueue()->latest('updated_at')->get();
        return view(
            'chat.chat',
            [
            'chatQueues' => $chatQueues,
            'chatPartnerName' => User::getMainChatUser(),
            ]
        );
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function setChatOnlineStatus(Request $request)
    {
        if (!User::isAdmin()) {
            throw new NotFoundHttpException();
        }
        Validator::make($request->all(), [
            'is_online' => 'required|string'
        ])->validate();
        $redis = Redis::connection()->client();
        if (boolean_value($request['is_online']) == true) {
            $redis->set('chat_status', 'start');
            return ['status' => 'Chat is online now'];
        } else {
            $redis->del('chat_status');
            return ['status' => 'Chat is offline now'];
        }
    }

    public function userOnlineStatus(Request $request)
    {
        if (!User::isAdmin()) {
            throw new NotFoundHttpException();
        }

        $users = User::all();
        foreach ($users as $user) {
            $user->last_seen = Carbon::parse($user->last_seen)->diffForHumans();
            if (Cache::has('user-is-online-' . $user->id)) {
                $user->online = true;
            } else {
                $user->online = false;
            }
        }

        return view(
            'chat.user-online',
            [
                'users' => $users,
            ]
        );
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function changeUser(Request $request)
    {
        $this->validate($request, [
            'queue_id' => 'required|number',
            'receiver_id' => 'required|number',
        ]);

    }
}
