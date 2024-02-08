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
        Schema::create('game_registration', function (Blueprint $table) {
            $table->bigIncrements('gr_id');
            $table->integer('gr_gd_id')->unsigned()->nullable()->comment('reference to game_data');
            $table->integer('gr_ui_id')->unsigned()->nullable()->comment('reference to user_information');
            $table->boolean('gr_is_outfield')->nullable();
            $table->decimal('gr_amount', 12, 2)->nullable();
            $table->string('gr_transaction_number', 200)->nullable();
            $table->integer('gr_created_by')->unsigned()->nullable();
            $table->timestamp('gr_created_at')->useCurrent()->nullable()->default(DB::raw('now()'));
            $table->integer('gr_updated_by')->unsigned()->nullable();
            $table->timestamp('gr_updated_at')->useCurrent()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_registration');
    }
};
