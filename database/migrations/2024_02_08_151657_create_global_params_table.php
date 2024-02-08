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
        Schema::create('mr_global_param', function (Blueprint $table) {
            $table->bigIncrements('mgp_id');
            $table->integer('mgp_code_id')->unsigned()->nullable();
            $table->string('mgp_slug', 100)->nullable();
            $table->string('mgp_name', 255)->nullable();
            $table->text('mgp_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mr_global_param');
    }
};
