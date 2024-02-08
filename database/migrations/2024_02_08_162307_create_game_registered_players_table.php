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
        Schema::create('game_registered_player', function (Blueprint $table) {
            $table->bigIncrements('grp_id');
            $table->integer('grp_gd_id')->unsigned()->nullable()->comment('reference to game_data');
            $table->integer('grp_ui_id')->unsigned()->nullable()->comment('reference to user_information');
            $table->boolean('grp_is_outfield')->nullable();
            $table->decimal('grp_amount_paid', 12, 2)->nullable();
            $table->dateTime('grp_paid_at')->nullable();
            $table->string('grp_transaction_number', 200)->nullable();
            $table->integer('grp_created_by')->unsigned()->nullable();
            $table->timestamp('grp_created_at')->useCurrent()->nullable()->default(DB::raw('now()'));
            $table->integer('grp_updated_by')->unsigned()->nullable();
            $table->timestamp('grp_updated_at')->useCurrent()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_registered_player');
    }
};
