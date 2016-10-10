<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTyperocketMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_media', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('path');
            $table->string('file');
            $table->string('ext');
            $table->string('caption');
            $table->string('alt');
            $table->jsonb('meta');
            $table->jsonb('sizes');
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
        Schema::dropIfExists('tr_media');
    }
}
