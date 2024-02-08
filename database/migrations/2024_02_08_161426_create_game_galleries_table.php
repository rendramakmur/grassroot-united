<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('game_gallery', function (Blueprint $table) {
            $table->bigIncrements('gg_id');
            $table->integer('gg_gd_id')->unsigned()->nullable()->comment('reference to game_data');
            $table->string('gg_image_url', 500)->nullable();
            $table->string('gg_alt_image', 255)->nullable();
            $table->integer('gg_created_by')->unsigned()->nullable();
            $table->timestamp('gg_created_at')->useCurrent()->nullable()->default(DB::raw('now()'));
            $table->integer('gg_updated_by')->unsigned()->nullable();
            $table->timestamp('gg_updated_at')->useCurrent()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_gallery');
    }
};
