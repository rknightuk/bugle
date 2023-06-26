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
        Schema::create('attachment', function (Blueprint $table) {
            $table->id();
            $table->integer('profile_id')->nullable(false);
            $table->integer('post_id')->nullable(false);
            $table->string('file')->nullable(false);
            $table->text('alt')->nullable(false);
            $table->string('mime')->nullable(false);
            $table->string('width')->nullable(false);
            $table->string('height')->nullable(false);
            $table->text('blurhash')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachment');
    }
};
