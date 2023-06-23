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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('ap_id', 500)->nullable(false);
            $table->integer('profile_id')->nullable(false);
            $table->integer('post_id')->nullable(true);
            $table->integer('type')->default(1);
            $table->string('url', 500)->nullable(true);
            $table->string('actor', 500)->nullable(false);
            $table->text('content')->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
