<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rankings', function (Blueprint $table) {
            $table->id();
            $table->integer('ranking_publication_id')->default(0);
            $table->integer('ranking_category_id')->default(0);
            $table->integer('player1_id')->default(0);
            $table->integer('player2_id')->default(0);
            $table->integer('team_id')->default(0);
            $table->string('p1_country')->nullable();
            $table->string('p2_country')->nullable();
            $table->integer('confederation_id')->default(0);
            $table->integer('match_party_id')->default(0);
            $table->string('team_ms')->nullable();
            $table->string('team_ws')->nullable();
            $table->string('team_md')->nullable();
            $table->string('team_wd')->nullable();
            $table->string('team_xd')->nullable();
            $table->string('team_sc')->nullable();
            $table->string('team_tc')->nullable();
            $table->string('team_uc')->nullable();
            $table->string('team_total_points')->nullable();
            $table->integer('rank')->default(0);
            $table->integer('rank_previous')->default(0);
            $table->string('qual')->nullable();
            $table->integer('points')->default(0);
            $table->integer('tournaments')->default(0);
            $table->string('close')->nullable();
            $table->integer('rank_change')->default(0);
            $table->string('win')->nullable();
            $table->string('lose')->nullable();
            $table->string('prize_money')->nullable();
            $table->integer('player1_model')->default(0);
            $table->integer('player2_model')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rankings');
    }
};
