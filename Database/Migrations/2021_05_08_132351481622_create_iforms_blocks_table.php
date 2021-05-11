<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIformsBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iforms__blocks', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('sort_order')->unsigned()->nullable()->default(0);
            $table->integer('form_id')->unsigned()->nullable();
            $table->foreign('form_id')->references('id')->on('iforms__forms')->onDelete('cascade');
            $table->text('options')->nullable();
            // Your fields
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
        Schema::table('iforms__blocks', function (Blueprint $table) {
            $table->dropForeign(['form_id']);
        });
        Schema::dropIfExists('iforms__blocks');
    }
}