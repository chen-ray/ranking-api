<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * @method static find($id)
 */
class Ranking extends Model
{

    protected $fillable = ["id", "ranking_publication_id", "ranking_category_id", "player1_id",
        "player2_id", "team_id", "p1_country", "p2_country", "confederation_id", "match_party_id",
        "team_ms", "team_ws", "team_md", "team_wd", "team_xd", "team_sc", "team_tc", "team_uc",
        "team_total_points", "rank", "rank_previous", "qual", "points", "tournaments",
        "close", "rank_change", "win", "lose", "prize_money"
    ];

    /**
     * 默认预加载的关联。
     *
     * @var array
     */
    protected $with = ['player1.withCountry', 'player2.withCountry'];

    /**
     * 球员1关联
     */
    public function player1(): HasOne
    {
        return $this->hasOne(Player::class, 'id', 'player1_id')->withDefault();
    }

    public function player2(): HasOne
    {
        return $this->hasOne(Player::class, 'id', 'player2_id');
    }

    public static function store($data){
        if( ! is_array($data)) {
            Log::error('ranking store 参数 data 不是数组');
            return;
        }
        try {
            $value = Cache::get('ranking_' . $data['id']);
            if($value !== null) {
                Log::error('ranking data exist');
                return;
            }
            $data = array_filter($data);
            //Log::debug('data=>',$data);

            $model = Ranking::find($data['id']);
            if($model !== null) {
                Log::debug('ranking data exist, id=>' . $data['id']);
                return;
            }
            $ranking    = new Ranking();
            $ranking->fill($data);
            $ranking->save();
            if($ranking->rank < 100) {
                // 只有前100名的选手才查询即时的 breakdown
                // 分派任务
                dispatch(new \App\Jobs\Breakdown($ranking));
            }

            Cache::put('ranking_' . $data['id'], $data, 30);
            $player1    = $data['player1_model'];

            Player::store($player1);
            if(key_exists( 'player2_model', $data)) {
                Player::store($data['player2_model']);
            }
        } catch (InvalidArgumentException|Exception $e) {
            Log::error($e);
        }
    }
}
