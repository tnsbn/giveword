<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ApiController extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /**
     * @throws Exception
     */
    public function __construct()
    {
    }
}
