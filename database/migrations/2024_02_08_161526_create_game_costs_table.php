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
        Schema::create('game_cost', function (Blueprint $table) {
            $table->bigIncrements('gc_id');
            $table->integer('gc_gd_id')->unsigned()->nullable()->comment('reference to game_data');
            $table->text('gc_description')->nullable();
            $table->decimal('gc_amount', 12, 2)->nullable();
            $table->integer('gc_created_by')->unsigned()->nullable();
            $table->timestamp('gc_created_at')->useCurrent()->nullable()->default(DB::raw('now()'));
            $table->integer('gc_updated_by')->unsigned()->nullable();
            $table->timestamp('gc_updated_at')->useCurrent()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_cost');
    }
};
