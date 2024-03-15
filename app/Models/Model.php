<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
{
    use HasFactory;


    protected static function handleJsonNull($data) {
        foreach ($data as $key => $datum) {
            if ( !$datum ) {
                unset($data[$key]);
            }
        }
    }
}
