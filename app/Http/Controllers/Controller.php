<?php

namespace App\Http\Controllers;

use App\Http\Middleware\ActivityByUser;
use Aws\DynamoDb\DynamoDbClient;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redis;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    public DynamoDbClient $dynamo;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->middleware(ActivityByUser::class);
        $this->cacheImages();
    }

    public static function cacheImages()
    {
        try {
            $redis = Redis::connection()->client();
            if (!$redis->exists('thumbs') || empty($redis->hKeys('thumbs'))) {
                $files = glob(App::publicPath('/images/thumbs/thumb*.jpg'));
                foreach ($files as $file) {
                    $name = basename($file);
                    if (!$redis->hExists('thumbs', $name)) {
                        $img = File::get($file);
                        $img = base64_encode($img);
                        $redis->hSet(
                            'thumbs',
                            $name,
                            'data:image/png;base64,' . $img
                        );
                    }
                }
            }
        } catch (\Exception $ex) {
        }
    }
}
