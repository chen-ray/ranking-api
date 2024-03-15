<?php

namespace App\Jobs;

use App\Models\Ranking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Breakdown implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Ranking $ranking
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $model  = \App\Models\Breakdown::where('ranking_id', $this->ranking->id)->first();
        if($model) {
            Log::info('Breakdown 对战数据已存在，跳出');
            return;
        }

        //$data = '{"rankId":2,"catId":7,"playerData":{"id":14109006,"ranking_publication_id":2408,"ranking_category_id":7,"player1_id":87442,"player2_id":null,"team_id":null,"p1_country":"KOR","p2_country":null,"confederation_id":2,"match_party_id":92819,"team_ms":null,"team_ws":null,"team_md":null,"team_wd":null,"team_xd":null,"team_sc":null,"team_tc":null,"team_uc":null,"team_total_points":null,"rank":1,"rank_previous":1,"qual":null,"points":"113314.0000","tournaments":17,"close":null,"rank_change":0,"win":null,"lose":null,"prize_money":null,"player1_model":{"id":87442,"code":"F01113A4-2115-4611-8923-85E45C4A2193","first_name":"Se Young","last_name":"AN","name_type_id":1,"slug":"an-se-young","name_display":"AN Se Young","name_initials":"AS","name_short1":"AN S Y ","name_short2":"AN","name_locked":0,"active":1,"profile_type":0,"avatar_id":null,"last_crawled_at":"2023-03-12 17:16:40","last_cache_updated_at":null,"old_member_id":87442,"gender_id":2,"date_of_birth":"2002-02-05 00:00:00","nationality":"KOR","country":"KOR","country_id":null,"creator_id":null,"ordering":null,"status":1,"para":0,"preferred_name":1,"is_deleted":0,"language":0,"image_profile_id":42062,"image_hero_id":42063,"created_at":"2023-03-13T12:41:18.000000Z","updated_at":"2023-10-20T01:27:04.000000Z","extranet_id":"87442","name_display_bold":"<span class=\"name-2\">AN</span> <span class=\"name-1\">Se Young</span>","country_model":{"id":2,"name":"Korea","code_iso3":"KOR","custom_code":"KOR","flag_name":"korea.png","flag_name_svg":"south-korea.svg","flag_url":"/uploads/flag/korea.png","ma_id":102,"confed_id":2,"currency_code":"","currency_name":"","currency_symbol":"","language_name":"Korean","nationality":"Korean","status":1,"is_deleted":0,"created_at":"2014-07-29T10:30:58.000000Z","updated_at":"2017-08-22T05:47:36.000000Z","created_by":null,"updated_by":1,"extranet_id":"2"}},"player2_model":null}}';
        $data = [
            "rankId"    => $this->ranking->confederation_id,
            //"rankId"    => 2,
            "catId"     => $this->ranking->ranking_category_id,
            "playerData"    => [
                "rank"  => $this->ranking->rank,
                "rank_previous" => $this->ranking->rank_previous,
                "id"        => $this->ranking->id,
                "ranking_category_id"   => $this->ranking->ranking_category_id,
                "ranking_publication_id"=> $this->ranking->ranking_publication_id,
                "player1_id"            => $this->ranking->player1_id,
                "player2_id"            => $this->ranking->player2_id ?: '',
            ]
        ];
        // {"rankId":2,"catId":7,"playerData":{"rank":1,"rank_previous":1,"id":14109006,
        //"ranking_category_id":7,"ranking_publication_id":2408,"player1_id":87442,"player2_id":0}}

        //Log::info('data', $data);
        $response = Http::withHeaders([
            'Authorization' => 'Bearer 2|NaXRu9JnMpSdb8l86BkJxj6gzKJofnhmExwr8EWkQtHoattDAGimsSYhpM22a61e1crjTjfIGTKfhzxA'
        ])->post('https://extranet-lv.bwfbadminton.com/api/vue-rankingbreakdown', $data);
        if($response->successful()){
            $json       = $response->json();
            $at         = date('Y-m-d H:i:s');
            foreach ($json as $key => $item) {
                $json[$key]['ranking_id'] = $this->ranking->id;
                $json[$key]['created_at'] = $at;
                $json[$key]['updated_at'] = $at;
            }
            $model  = new \App\Models\Breakdown();
            $model->insert($json);
        } else {
            Log::error('return status not 2xx');
            Log::error('code:'. $response->status());
            Log::error($response);
        }
    }
}
