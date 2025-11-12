<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('words')) {
            Schema::create('words', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->string('message', 2550)->fulltext();
                $table->integer('price')->default(1);
                $table->string('tags', 255)->nullable()->fulltext();
                $table->timestamps();
                $table->softDeletes();
            });
            DB::statement('ALTER TABLE words ADD FULLTEXT words_fulltext (message, tags)');
        }
    }
};
