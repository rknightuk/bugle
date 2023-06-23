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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->integer('profile_id')->nullable(false);
            $table->uuid('uuid')->nullable(false);
            $table->string('content', 500)->nullable(false);
            $table->boolean('featured')->default(false);
            $table->integer('visibility')->default(0);
            $table->boolean('sensitive')->default(false);
            $table->string('spoiler_text', 100)->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
