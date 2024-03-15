<?php

namespace App\Jobs;

use App\Models\Week;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CrawlerWeeks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $url = 'https://extranet-lv.bwfbadminton.com/api/vue-rankingweek';

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::debug("enter CrawlerWeeks handle");
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer 2|NaXRu9JnMpSdb8l86BkJxj6gzKJofnhmExwr8EWkQtHoattDAGimsSYhpM22a61e1crjTjfIGTKfhzxA'
            ])->post($this->url, ['rankId' => 2]);
            if($response->successful()){
                Log::debug('return is 200');
                $json       = $response->json();
                foreach ($json as $item) {
                    $key    = ['id' => $item['id']];
                    unset($item['id']);
                    Week::updateOrCreate($item, $key);
                }
            } else {
                Log::error('return status not 2xx');
                Log::error('code:'. $response->status());
                Log::error($response);
            }
            Log::info( 'BadmintonCrawler handle finish');
        } catch (\Exception $e){
            Log::error($e);
        }
    }
}
