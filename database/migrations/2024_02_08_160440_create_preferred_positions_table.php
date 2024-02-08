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
        Schema::create('preferred_position', function (Blueprint $table) {
            $table->bigIncrements('pp_id');
            $table->integer('pp_ui_id')->comment('reference to user_information');
            $table->integer('pp_position')->unsigned()->nullable()->comment('reference to global_param - mr_position');
            $table->integer('pp_created_by')->unsigned()->nullable();
            $table->timestamp('pp_created_at')->useCurrent()->nullable()->default(DB::raw('now()'));
            $table->integer('pp_updated_by')->unsigned()->nullable();
            $table->timestamp('pp_updated_at')->useCurrent()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preferred_position');
    }
};
