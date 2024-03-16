<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RankingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data   = [
            'id'            => $this->id,
            'rank'          => $this->rank,
            'rank_change'   => $this->rank_change,
            'tournaments'   => $this->tournaments,
            'points'        => $this->points,
            'player1_name'  => $this->player1->name,
            'country_img'   => 'https://blog-cdn.chen-ray.cn/countries/' . $this->player1->withCountry->flag_name_svg,
            'country_name'  => $this->player1->withCountry->name,
            'country_id'    => $this->player1->country_id,
            'p1_country'    => $this->p1_country,
            'player1_birth' => $this->player1->date_of_birth,
            'player2_name'  => null,
            'player2_birth' => null,
        ];
        if($this->player2) {
            $data['player2_name']   = $this->player2->name;
            $data['player2_birth']  = $this->player2->date_of_birth;
        }
        return $data;
    }
}
