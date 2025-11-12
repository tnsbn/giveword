<?php

namespace App\Http\Controllers;

use App\Models\Word;
use App\Http\Controllers\Controller as AppController;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class WritingController extends AppController
{
    /**
     * Where to redirect users after posting message.
     *
     * @var string
     */
    protected string $redirectTo = '/handbook';

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
     * Get a validator for an incoming posting request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make(
            $data,
            [
            'message' => 'required|string|max:2550',
            //            'price' => 'required|integer',
            'tags' => 'nullable|string|max:255',
            ]
        );
    }

    /**
     * Allow user to write something.
     *
     * @param Request $request
     * @return Renderable
     */
    public function index(Request $request): Renderable
    {
        return view('writing');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function writing(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validator($request->all())->validate();

            $data = $request->all();
            $word = new Word(
                [
                'user_id' => Auth::user()->id,
                'message' => strip_tags($data['message'], '<br>'),
                'price' => 1,
                'tags' => tagsToDbString($data['tags'] ?? null)
                ]
            );

            try {
                $word = $word->saveOrFail();
                if (isset($data['tags']) && !empty($data['tags']) && $word) {
                    Word::cacheTags();
                }
                return redirect($this->redirectPath());
            } catch (\Throwable $exception) {
                return redirect($this->redirectPath(), 500, ['data' => $word]);
            }
        }

        return redirect($this->redirectPath());
    }

    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectPath(): string
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/welcome';
    }
}
