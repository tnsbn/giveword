<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Support\Renderable;

class HomeController extends Controller
{
    /**
     * Show the homepage.
     *
     * @return Renderable
     */
    public function index()
    {
        return view('welcome', [
            'chatPartnerName' => User::getMainChatUser(),
        ]);
    }
}
