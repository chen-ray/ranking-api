<?php

namespace App\Support;

use App\Services\Qcloud\Cos;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CountryCosUrl
{
    protected Cos $client;
    protected String $url;

    public function __construct(string $key) {

        if(Str::startsWith($key, 'http')) {
            $arr = explode('/', $key);
            $key = $arr[count($arr)-1];
        }

        $cacheKey = 'countryImgCosUrl_' . $key;

        $cache  = Cache::get($cacheKey);
        if ($cache) {
             Log::debug('cos url 有缓存了');
             $this->url = $cache;
        }else {
            $this->client   = new Cos();
            $this->url      = $this->client->getObjectUrl($key);
            Cache::put($cacheKey, $this->url, now()->addDay());
            Log::debug('CountryCosUrl __construct url=>');
        }
    }

    public function __toString(): string
    {
        // TODO: Implement __toString() method.
        return $this->url;
    }
}
