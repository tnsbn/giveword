<?php

namespace App\Http\Controllers\Api;

use App\Models\Word;
use App\Helpers\ImageToNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HomeController extends ApiController
{
    /**
     * @param Request $request
     * @return array
     */
    public function randomWord(Request $request): array
    {
        if ($request->ajax() && $request->isMethod('post')) {
            if (!$request->has('_token') || !$request->has('salt')) {
                return [
                    'error' => 'Wrong request!',
                ];
            }
            $rd = 0;
            $tk = $request->get('_token') . $request->get('salt') . time();
            for ($i = 0; $i < strlen($tk); $i++) {
                $rd += intval(bin2hex($tk[$i]));
            }
            $count = Word::all()->count();
            $word = Word::find($rd % $count);
            return [
                'msg' => $word->message
            ];
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public function wordByDraw(Request $request): array
    {
        if ($request->ajax() && $request->isMethod('post')) {
            if (!$request->has('_token') || !$request->has('img')) {
                return [
                    'error' => 'Wrong request!',
                ];
            }
            $filepath = '';
            try {
                $filepath = App::storagePath('/tmp/draw/') . $request->get('_token') . '.jpg';
                $base64 = $request->get('img');
                $img = base64_decode($base64);
                $img = file_put_contents($filepath, $img);
                if ($img) {
                    $genNumber = ImageToNumber::genImage($filepath);
                    if ($genNumber > 0) {
                        $count = Word::all()->count();
                        $id = $genNumber % $count;
                        $id = $id <= 0 ? 1 : $id;
                        $word = Word::find($id);
                        return [
                            'msg' => $word->message
                        ];
                    } else {
                        return [
                            'error' => 'Empty image'
                        ];
                    }
                } else {
                    return [
                        'error' => 'File error! Please try again.',
                    ];
                }
            } catch (\Throwable $ex) {
//                dump($ex);
                return [
                    'error' => 'Server is busy! Please try again.',
                ];
            } finally {
                if (File::exists($filepath)) {
                    File::delete($filepath);
                }
            }
        } else {
            throw new NotFoundHttpException();
        }
    }
}
