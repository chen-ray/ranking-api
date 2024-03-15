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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('code_iso3')->nullable();
            $table->string('custom_code')->nullable();
            $table->string('flag_name')->nullable();
            $table->string('flag_name_svg')->nullable();
            $table->string('flag_url')->nullable();
            $table->integer('ma_id')->default(0);
            $table->integer('confed_id')->default(0);
            $table->string('currency_code')->nullable();
            $table->string('currency_name')->nullable();
            $table->string('currency_symbol')->nullable();
            $table->string('language_name')->nullable();
            $table->string('nationality')->nullable();
            $table->integer('status')->default(0);
            $table->integer('is_deleted')->default(0);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('extranet_id')->nullable();
            $table->string('chinese_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
