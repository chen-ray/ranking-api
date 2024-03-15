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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('chinese_name')->nullable();
            $table->smallInteger('name_type_id')->default(0);
            $table->string('slug')->nullable();
            $table->string('name_display')->nullable();
            $table->string('name_initials')->nullable();
            $table->string('name_short1')->nullable();
            $table->string('name_short2')->nullable();

            $table->smallInteger('name_locked')->default(0);
            $table->smallInteger('active')->default(0);
            $table->smallInteger('profile_type')->default(0);
            $table->smallInteger('avatar_id')->default(0);

            $table->dateTime('last_crawled_at')->nullable();
            $table->dateTime('last_cache_updated_at')->nullable();
            $table->integer('old_member_id')->default(0);
            $table->smallInteger('gender_id')->default(0);
            $table->date('date_of_birth')->nullable();

            $table->string('nationality')->nullable();
            $table->string('country')->nullable();
            $table->string('country_id')->nullable();
            $table->string('creator_id')->nullable();
            $table->string('ordering')->nullable();

            $table->smallInteger('status')->default(0);
            $table->integer('para')->default(0);
            $table->smallInteger('preferred_name')->default(0);
            $table->smallInteger('is_deleted')->default(0);
            $table->smallInteger('language')->default(0);
            $table->integer('image_profile_id')->default(0);
            $table->integer('image_hero_id')->default(0);

            //$table->dateTime('created_at')->nullable();
            //$table->dateTime('updated_at')->nullable();
            $table->integer('extranet_id')->nullable();
            $table->string('name_display_bold')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
