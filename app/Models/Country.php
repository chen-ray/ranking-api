<?php

namespace App\Models;

use App\Jobs\HandleCountryImg;
use App\Support\CountryCosUrl;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Psr\SimpleCache\InvalidArgumentException;

class Country extends Model
{

    protected $fillable = ["id", "name", "code_iso3", "custom_code", "flag_name", "flag_name_svg",
                "flag_url", "ma_id", "confed_id", "currency_code", "currency_name",
                "currency_symbol", "language_name", "nationality", "status", "is_deleted",
                "created_at", "updated_at", "created_by", "updated_by", "extranet_id"
    ];


    /**
     * 获取用户的名字。
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['chinese_name'] ? $attributes['chinese_name'] :$attributes['name'] ,
        );
    }

/*    protected function imgCosUrl(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes)   => new CountryCosUrl(
                $attributes['flag_name_svg'],
            ),
        );
    }*/

    public static function store($data){
        if( ! is_array($data)) {
            Log::error('country store 参数 data 不是数组');
            return;
        }
        try {
            $value = Cache::get('country_' . $data['id']);
            if($value !== null) {
                //Log::debug('country data exist');
                return;
            }
            $data = array_filter($data);

            $model = Country::find($data['id']);
            if($model !== null) {
                Log::debug('Country data exist, id=>' . $data['id']);
                return;
            }

            $model  = new Country();
            $model->fill($data);
            $model->save();
            dispatch(new HandleCountryImg($model));
            Cache::put('country_' . $data['id'], $data, 30);
        } catch (InvalidArgumentException $e) {
            Log::error($e);
        }
    }
}
