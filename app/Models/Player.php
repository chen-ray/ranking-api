<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Psr\SimpleCache\InvalidArgumentException;

class Player extends Model
{
    protected $fillable = [ "id", "code", "first_name", "last_name", "name_type_id", "slug", "name_display",
            "name_initials", "name_short1", "name_short2", "name_locked", "active", "profile_type",
            "avatar_id", "last_crawled_at", "last_cache_updated_at", "old_member_id", "gender_id",
            "date_of_birth", "nationality", "country", "country_id", "creator_id", "ordering", "status",
            "para", "preferred_name", "is_deleted", "language", "image_profile_id", "image_hero_id",
            "created_at", "updated_at", "extranet_id", "name_display_bold"
    ];

    /**
     * 默认预加载的关联。
     *
     * @var array
     */
    protected $with = ['withCountry'];

    /**
     * 属性访问器
     * 获取用户的名字。
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) =>
            key_exists('chinese_name', $attributes) && $attributes['chinese_name'] ? $attributes['chinese_name'] :$attributes['name_display']
        );
    }


    public function withCountry(): HasOne
    {
        return $this->hasOne(Country::class, 'id', 'country_id')->withDefault();
    }

    public static function store($data){
        if( ! is_array($data)) {
            Log::error('player store 参数 data 不是数组');
            return;
        }
        //Log::debug('player store', $data);
        try {
            $value = Cache::get('player_' . $data['id']);
            if($value !== null) {
                Log::debug('player data exist');
                return;
            }

            $data               = array_filter($data);
            $data['country_id'] = $data['country_model']['id'];
            $model = Player::find($data['id']);
            if($model !== null) {
                Log::debug('Player data exist, id=>' . $data['id']);
                return;
            }

            $model  = new Player();
            $model->fill($data);
            $model->save();

            Cache::put('player_' . $data['id'], $data, 30);
            $country    = $data['country_model'];
            Country::store($country);
        } catch (InvalidArgumentException $e) {
            Log::error($e);
        }
    }
}
