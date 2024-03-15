<?php

namespace App\Jobs;

use App\Models\Country;
use App\Services\Qcloud\Cos;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HandleCountryImg implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     */
    public function __construct(
        public Country $country
    )
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //Log::debug('enter job HandleCountryImg handle');
        //https://extranet.bwf.sport/docs/flags-svg/india_1.svg

        $folderPath = storage_path('framework/cache/country/');
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
            Log::debug( '文件夹创建成功！');
        } else {
            Log::debug( '文件夹已存在！');
        }

        $result = Str::startsWith($this->country->flag_name_svg, 'http');
        if($result === true) {
            return;
        }
        // 处理
        $url    = 'https://extranet.bwf.sport/docs/flags-svg/' . $this->country->flag_name_svg;
        Log::debug('url=>' . $url);

        $path   = storage_path('framework/cache/country/' . $this->country->flag_name_svg);
        $content = file_get_contents($url);
        file_put_contents($path, $content);
        $cos    = new Cos();
        $cos->upload($path, $this->country->flag_name_svg);
        $url    = $cos->getObjectUrlWithoutSign($this->country->flag_name_svg);
        Log::debug('cos url=>' . $url);
        $this->country->flag_name_svg = $url;
        $this->country->save();
        Log::debug( 'HandleCountryImg Job finished！id=>' . $this->country->id);
    }
}
