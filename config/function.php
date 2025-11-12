<?php

use Illuminate\Support\Facades\Redis;

if (!function_exists('tagsToArray')) {
    /**
     * @param mixed $tags String of tags or array of tags
     * @return array Array of formatted tag names
     */
    function tagsToArray(mixed $tags): array
    {
        $tags = $tags ?? "";
        if (is_array($tags)) {
            $arr = $tags;
        } else {
            $arr = explode(",", $tags);
        }
        $arr = array_map(function ($ele) {
            $ele = preg_replace("/[^A-Za-z]/", " ", $ele);
            $ele = preg_replace("/\s\s+/", " ", trim($ele));
            return $ele;
        }, $arr);

        return array_unique(array_filter($arr));
    }
}

if (!function_exists('tagsToDbString')) {
    /**
     * Format tags before inserting into database
     * @param mixed $tags String of tags or array of tags
     * @return string Formatted tag names
     */
    function tagsToDbString(mixed $tags): string
    {
        $arr = tagsToArray($tags);
        if (!empty($arr)) {
            return "," . implode(",", $arr) . ",";
        }

        return "";
    }
}

if (!function_exists('tagsToViewString')) {
    /**
     * Format tags before rendering to view
     * @param mixed $tags String of tags or array of tags
     * @return string Formatted tag names
     */
    function tagsToViewString(mixed $tags): string
    {
        $arr = tagsToArray($tags);
        if (!empty($arr)) {
            return implode(", ", $arr);
        }

        return "";
    }
}

if (!function_exists('getCachedThumb')) {
    /**
     * @param string $key
     * @param string $default
     * @return string
     */
    function getCachedThumb(string $key, string $default): string
    {
        $src = $default;
        try {
            $redis = Redis::connection()->client();
            if ($redis->hExists('thumbs', $key)) {
                $src = $redis->hGet('thumbs', $key);
                $src = $src === false ? $default : $src;
            }
        } catch (\Exception $ex) {
        }

        return $src;
    }
}

if (!function_exists('delRedisKeys')) {
    /**
     * @param string $pattern
     * @return void
     */
    function delRedisKeys(string $pattern)
    {
        try {
            $redis = Redis::connection()->client();
            foreach ($redis->keys($pattern) as $key) {
                $redis->del($key);
            }
            $redis->flushDB();
        } catch (\Exception $ex) {
        }
    }
}
