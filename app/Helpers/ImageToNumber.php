<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;

class ImageToNumber
{
    public static function genImage(string $filepath): int
    {
        $num = -1;
        $pyPath = App::basePath('/python');
//        $source = 'source ' . $pyPath . '/_env/bin/activate && ';
//        $deactivate = ' && deactivate';
//        $command = $source . 'python ' . $pyPath . '/image-to-number.py -i ' . $filepath . $deactivate;
        $command = 'python3 ' . $pyPath . '/image-to-number.py -i ' . $filepath;
        $output = [];
        $result_code = 0;
        exec($command, $output, $result_code);
//        dump($command);
//        dump($output);
//        dump($result_code);
        $prefix = 'number:';
        if ($result_code == 0 && count($output) > 0 && str_starts_with($output[0], $prefix)) {
            $num = intval(substr($output[0], strlen($prefix)));
        }
//        dump($num);
        return $num;
    }
}
