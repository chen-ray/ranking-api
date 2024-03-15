<?php

namespace App\Jobs;

use App\Models\Country;
use App\Models\Player;
use App\Models\Ranking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Laravel\Octane\Facades\Octane;

class BadmintonCrawler implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $url = 'https://extranet-lv.bwfbadminton.com/api/vue-rankingtable';
    private mixed $postData = [];
    private int $page;
    private int $catId;
    private int $publicationId;

    /**
     * Create a new job instance.
     */
    public function __construct($catId, $page = 1, $publicationId=0)
    {
        $this->page     = $page;
        $this->catId    = $catId;
        $this->publicationId = $publicationId;

        $this->postData = [
            "rankId"    => 2,
            "catId"     => $catId,
            "publicationId" => $publicationId,
            "doubles"   => false,
            "searchKey" =>"",
            "pageKey"   => "100",
            "page"      => $page,
            "drawCount" => 1
        ];
    }

    private int $manSingleCatId     = 6;
    private int $womanSingleCatId   = 7;
    private int $manDoublesCatId    = 8;
    private int $womanDoublesCatId  = 9;
    private int $mixedDoublesCatId  = 10;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::debug("enter BadmintonCrawler handle catId=[{$this->catId}], page=[{$this->page}]");
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer 2|NaXRu9JnMpSdb8l86BkJxj6gzKJofnhmExwr8EWkQtHoattDAGimsSYhpM22a61e1crjTjfIGTKfhzxA'
            ])->post($this->url, $this->postData);
            if($response->successful()){
                Log::debug('return is 200');
                $json       = $response->json();
                //Log::info($json);
                $lastPage   = $json['results']['last_page'];
                $data       = $json['results']['data'];

                foreach ($data as $item) {
                    Ranking::store($item);
                }

                if($lastPage == $this->page) {
                    Log::info('这是最后一页了，不再爬');
                } else {
                    $nextPage = $this->page+1;
                    Log::info("catId={$this->catId},当前页面={$this->page}, 共有 [{$lastPage}] 页 下一页={$nextPage}, 准备并发任务");
                    //$crawler = new BadmintonCrawler($this->catId, $nextPage);
                    // 10 秒后运行
                    BadmintonCrawler::dispatch($this->catId, $nextPage, $this->publicationId)->delay(now()->addSeconds(10));
                }
            } else {
                Log::error('return status not 2xx');
                Log::error('code:'. $response->status());
                Log::error($response);
            }
            Log::info( 'BadmintonCrawler handle finish');
        } catch (\Exception $e){
            Log::error('BadmintonCrawler handle error');
            Log::error($e);
        }
    }
}
