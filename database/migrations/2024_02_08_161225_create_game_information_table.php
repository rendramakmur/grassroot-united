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
        Schema::create('game_information', function (Blueprint $table) {
            $table->bigIncrements('gi_id');
            $table->integer('gi_gd_id')->unsigned()->nullable()->comment('reference to game_data');
            $table->integer('gi_info_type')->unsigned()->nullable()->comment('reference to mr_global_param - mr_game_info');
            $table->text('gi_description')->nullable();
            $table->integer('gi_created_by')->unsigned()->nullable();
            $table->timestamp('gi_created_at')->useCurrent()->nullable()->default(DB::raw('now()'));
            $table->integer('gi_updated_by')->unsigned()->nullable();
            $table->timestamp('gi_updated_at')->useCurrent()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_information');
    }
};
