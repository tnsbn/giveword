<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ChatController extends Controller
{
    public const CHANNEL_PREFIX = 'suw';
    public const CHAT_CHANNEL = 'chat_suw';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param  Request $request
     * @return string[]|void
     */
    #[ArrayShape(['status' => "string"])]
    public function ajaxStartChat(Request $request)
    {
        if (!$request->ajax()) {
            throw new NotFoundHttpException();
        }
        try {
            $adminId = (env('CHAT_UID') ?? 2);
            $redis = Redis::connection()->client();
            if (Auth::user()->id == $adminId) {
                if ($redis->get('chat_status') != 'start') {
                    $redis->set('chat_status', 'start');
                }
                return ['status' => 'start'];
            } elseif (Auth::user()->id != $adminId && $redis->get('chat_status') == 'start') {
                $redis->set('chat_guest_id', Auth::user()->id);
                $redis->set('chat_guest_name', Auth::user()->name);
                return ['status' => 'start'];
            }
        } catch (\Throwable $exception) {
            echo $exception->getMessage();
        }
    }

    /**
     * @param  Request $request
     * @return string[]|string
     */
    public function loadName(Request $request): array|string
    {
        if (!$request->ajax()) {
            throw new NotFoundHttpException();
        }
        try {
            $adminId = (env('CHAT_UID') ?? 2);
            $redis = Redis::connection()->client();
            if (Auth::user()->id == $adminId) {
                $name = $redis->get('chat_guest_name');
                if (!empty($name)) {
                    return ['name' => $name];
                }
                return 'Wrong request';
            } else {
                throw new NotFoundHttpException();
            }
        } catch (\Throwable $exception) {
            return 'Error';
        }
    }

    /**
     * @param  Request $request
     * @return array
     */
    public function send(Request $request): array
    {
        try {
            if ($request->isMethod('post') && $request->has('message')) {
                $redis = Redis::connection()->client();
                if ($redis->get('chat_status') == 'start') {
                    $adminId = (env('CHAT_UID') ?? 2);
                    $receiver = Auth::user()->id == $adminId ?
                        $redis->get('chat_guest_id') :
                        $adminId;
                    $message = $request->get('message');
                    $mqService = new \App\Services\RabbitMQService();
                    $msgInfo = $mqService->publish(
                        $message,
                        self::CHAT_CHANNEL,
                        Auth::user()->id,
                        $receiver
                    );
                    return [
                        'status' => 'sent',
                        'time' => $msgInfo['time'],
                    ];
                } else {
                    return [
                        'status' => 'error',
                        'error-msg' => 'Chat is disabled'
                    ];
                }
            } else {
                return [
                    'status' => 'error',
                    'error-msg' => 'Wrong request'
                ];
            }
        } catch (\Throwable $ex) {
            return [
                'status' => 'error',
                'error-msg' => 'Server error. Please try again later.'
            ];
        }
    }

    /**
     * @param  Request $request
     * @return array
     */
    public function receive(Request $request): array
    {
        try {
            if ($request->isMethod('post')) {
                $redis = Redis::connection()->client();
                if ($redis->get('chat_status') == 'start') {
                    $adminId = (env('CHAT_UID') ?? 2);
                    $sender = Auth::user()->id == $adminId ?
                        $redis->get('chat_guest_id') :
                        $adminId;
                    $userChannel = self::CHAT_CHANNEL . '_' . $sender .
                        '_' . Auth::user()->id;
                    $hKeys = $redis->hKeys($userChannel);
                    $messages = [];
                    foreach ($hKeys as $key) {
                        $messages[$key] = $redis->hGet($userChannel, $key);
                        $redis->hDel($userChannel, $key);
                    }
                    return [
                        'messages' => $messages,
                    ];
                } else {
                    return [
                        'status' => 'error',
                        'error-msg' => 'Chat is disabled'
                    ];
                }
            } else {
                return [
                    'status' => 'error',
                    'error-msg' => 'Wrong request'
                ];
            }
        } catch (\Throwable $ex) {
            return [
                'status' => 'error',
                'error-msg' => 'Server error. Please try again later.'
            ];
        }
    }
}
