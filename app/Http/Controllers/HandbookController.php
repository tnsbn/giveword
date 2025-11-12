<?php

namespace App\Http\Controllers;

use App\Helpers\ChatHelper;
use App\Http\Controllers\Word\WordCrud;
use App\Models\User;
use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class HandbookController extends Controller
{
    use WordCrud;

    protected array $itemPerPage = [
        'ipp5' => 5,
        'ipp10' => 10,
        'ipp15' => 15,
        'ipp20' => 20,
    ];

    private const REACH_CAN_CHAT = 3;

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
     * Show the words that user wrote.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $ipp = 'ipp' . request('ipp');
        $ipp = ($this->itemPerPage[$ipp] ?? null) ? $ipp : 'ipp5';

        /* @var Paginator $words */
        $words = Word::query()
            ->where('user_id', '=', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate($this->itemPerPage[$ipp]);
        if ($words->isEmpty() && $words->currentPage() != 1) {
//            return redirect($request->url());
        }

        $words = Word::addPaginateDetail($words);

        return view('handbook', ['kind' => 'handbook', 'words' => $words]);
    }

    /**
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function takenWords(Request $request)
    {
        $ipp = 'ipp' . request('ipp');
        $ipp = ($this->itemPerPage[$ipp] ?? null) ? $ipp : 'ipp5';

        /* @var Paginator $words */
        $words = Word::query()
            ->rightJoin('user_took_word', 'words.id', '=', 'user_took_word.word_id')
            ->where('user_took_word.user_id', '=', Auth::user()->id)
            ->select(['words.*'])
            ->orderBy('words.created_at', 'desc')
            ->paginate($this->itemPerPage[$ipp]);
        if ($words->isEmpty() && $words->currentPage() != 1) {
            return redirect($request->url());
        }
        $words = Word::addPaginateDetail($words);

        $redis = Redis::connection()->client();
//        $canChat = $this->checkCanChat();
        $isMainUser = Auth::user()->id == (env('CHAT_UID') ?? 2);
        $chatPartnerName = $isMainUser ?
            $redis->get('chat_guest_name') :
            User::getMainChatUser();
        $chatStatus = $this->getChatStatus();

        return view(
            'handbook',
            [
            'kind' => 'taken_words',
            'words' => $words,
            'canChat' => true,
            'chatStatus' => ChatHelper::isOnline(),
            'isMainUser' => $isMainUser,
            'chatPartnerName' => $chatPartnerName,
            ]
        );
    }

    /**
     * @return bool
     */
    private function checkCanChat(): bool
    {
        if (Auth::user()->id == (env('CHAT_UID') ?? 2)) {
            return true;
        }
        $count = Word::query()
            ->rightJoin('user_took_word', 'words.id', '=', 'user_took_word.word_id')
            ->where('user_took_word.user_id', '=', Auth::user()->id)
            ->where('words.user_id', '=', env('CHAT_UID') ?? 2)
            ->count();
        return $count >= self::REACH_CAN_CHAT;
    }

    /**
     * @return false|mixed|string
     */
    private function getChatStatus(): mixed
    {
        $redis = Redis::connection()->client();
        return !$redis->get('chat_status') ? 'idle' : $redis->get('chat_status');
    }
}
