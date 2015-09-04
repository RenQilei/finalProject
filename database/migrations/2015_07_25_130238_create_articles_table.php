<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
         * Create Content Table
         */
        Schema::create('articles', function(Blueprint $table) {
            $table->increments('id');
            $table->string('title');

            $table->integer('author')->unsigned();
            $table->foreign('author')->references('id')->on('users'); //暂时未考虑cascade删除

            $table->integer('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');

            $table->integer('template_id')->unsigned()->default(0);

            $table->timestamps();
        });

        /*
         * Create Content Section Table
         */
        Schema::create('article_sections', function(Blueprint $table) {
            $table->increments('id');
            $table->text('content');
            $table->integer('template_section_id')->unsigned()->default(0);

            $table->integer('article_id')->unsigned();
            $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');

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
        Schema::drop('article_sections');
        Schema::drop('articles');
    }
}
