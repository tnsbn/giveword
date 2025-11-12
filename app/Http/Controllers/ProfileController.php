<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public array $successMessage = [
        'prf_s1' => ['update_profile_success' => "Updated profile successfully!"],
        'prf_s2' => ['update_password_success' => "Updated password successfully!"],
        'prf_s3' => ['update_profile_success' => "Updated user name successfully!"],
    ];

    public array $errorMessage = [
        'prf_e1' => ['update_profile_error' => "Update profile failed!"],
        'prf_e2' => ['update_password_error' => "Update password failed!"],
        'prf_e3' => ['update_profile_error' => "Update user name failed!"],
    ];

    public array $profileMessage;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->profileMessage = $this->successMessage + $this->errorMessage;
    }

    /**
     * Show profile of user.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function showProfile(Request $request)
    {
        $query = $request->all();
        $codes = array_keys($query);
        $codes = array_unique(array_filter($codes));
        $data = [];
        foreach ($codes as $code) {
            if ($this->profileMessage[$code] ?? null) {
                $data[key($this->profileMessage[$code])] = $this->profileMessage[$code][
                    key($this->profileMessage[$code])
                ];
            }
        }
        $data = array_unique(array_filter($data));

        if (Auth::user()->id == (env('CHAT_UID') ?? 2)) {
            try {
                $redis = Redis::connection()->client();
                if (isset($request['delete-redis'])) {
                    delRedisKeys('*');
                }
                if (isset($request['delete-chat-status'])) {
                    $redis->del('chat_status');
                }
                dump($redis->keys('*'));
                //        exit();
            } catch (\Throwable $ex) {
            }
        }

        return view('profile', $data);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function passwordValidator(array $data)
    {
        return Validator::make(
            $data,
            [
            'current' => [
                'required',
                'string',
                'min:6',
                function ($attribute, $value, $fail) {
                    if (!(Hash::check($value, Auth::user()->password))) {
                        return $fail("Wrong current password!");
                    }
                }
            ],
            'password' => 'required|string|min:6|confirmed',
            ]
        );
    }

    /**
     * @param  array $data
     * @return \Illuminate\Validation\Validator
     */
    protected function profileValidator(array $data)
    {
        return Validator::make(
            $data,
            [
            'name' => 'required|string|max:255'
            ]
        );
    }

    /**
     * Allow user to change password.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function changePassword(Request $request)
    {
        $this->passwordValidator($request->all())->validate();

        $user = Auth::user();
        $data = $request->all();

        $data['password'] = bcrypt($data['password']);
        if ($user->update($data)) {
            return redirect(route('show_profile') . "?prf_s2");
        }

        return redirect(route('show_profile') . "?prf_e2");
    }

    /**
     * Allow user to change name.
     *
     * @param  Request $request
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    public function changeName(Request $request)
    {
        $this->profileValidator($request->all())->validate();
        $user = Auth::user();
        $data = $request->all();
        if ($user->update($data)) {
            return [
                "redirect" => true,
                "redirect_url" => route('show_profile') . "?prf_s3",
            ];
        }

        return [
            "redirect" => true,
            "redirect_url" => route('show_profile') . "?prf_e3",
        ];
    }

    /**
     * @param  Request $request
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateProfile(Request $request)
    {
        $this->profileValidator($request->all())->validate();
        $user = Auth::user();
        $data = $request->all();
        if ($user->update($data)) {
            return [
                "redirect" => true,
                "redirect_url" => route('show_profile') . "?prf_s1",
            ];
        }

        return [
            "redirect" => true,
            "redirect_url" => route('show_profile') . "?prf_e1",
        ];
    }
}
