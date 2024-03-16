<?php

namespace App\Jobs;

use App\Models\Country;
use App\Services\Qcloud\Cos;
use Exception;
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
        //https://extranet.bwf.sport/docs/flags-svg/india_1.svg
        try{
            $folderPath = storage_path('framework/cache/country/');
            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0777, true);
                Log::debug( '文件夹创建成功！');
            }

            $svg = $this->country->flag_name_svg;
            if(Str::startsWith($this->country->flag_name_svg, 'http')) {
                $tmp = explode('/', $this->country->flag_name_svg);
                $svg = end($tmp);
            }

            $path   = storage_path('framework/cache/country/' . $svg);
            if ( !file_exists($path) ) {
                // if file not exists , get it
                $url    = 'https://extranet.bwf.sport/docs/flags-svg/' . $svg;
                Log::debug('url=>' . $url);

                $content = file_get_contents($url);
                file_put_contents($path, $content);
            }

            $cos    = new Cos();
            $key    = 'countries/' . $svg;
            if($cos->doesObjectExist($key)) {
                Log::debug('COS already has this object ');
            } else {
                $cos->upload($path, $key);
            }

            $this->country->flag_name_svg = $svg;
            $this->country->save();
        }catch (Exception $e) {
            Log::error('HandleCountryImg handle error=>' . $e->getTraceAsString());
            Log::error($e);
        } finally {
            Log::debug( 'HandleCountryImg Job finished！id=>' . $this->country->id);
        }

    }
}
