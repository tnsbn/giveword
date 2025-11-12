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
        Schema::create('user_took_word', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('word_id');
            $table->unique(['user_id', 'word_id']);
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
