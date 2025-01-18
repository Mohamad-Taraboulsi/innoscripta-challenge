<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article', function (Blueprint $table) {
            $table->id();
            $table->string('api_source');
            $table->string('source_id')->nullable();
            $table->string('source_name')->nullable();
            $table->string('author')->nullable();
            $table->string('title');
            $table->string('type')->nullable();
            $table->text('description')->nullable();
            $table->string('url')->unique();
            $table->text('url_to_image')->nullable();
            $table->dateTime('published_at');
            $table->text('content')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article');
    }
};
