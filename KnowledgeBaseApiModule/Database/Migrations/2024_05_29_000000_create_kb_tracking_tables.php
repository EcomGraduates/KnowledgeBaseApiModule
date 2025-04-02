<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKbTrackingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Table for tracking category views
        Schema::create('kb_category_views', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->unsigned();
            $table->integer('mailbox_id')->unsigned();
            $table->integer('view_count')->default(0);
            $table->timestamps();
            
            $table->unique(['category_id', 'mailbox_id']);
            $table->index('view_count');
        });

        // Table for tracking article views
        Schema::create('kb_article_views', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('article_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->integer('mailbox_id')->unsigned();
            $table->integer('view_count')->default(0);
            $table->timestamps();
            
            $table->unique(['article_id', 'category_id', 'mailbox_id']);
            $table->index('view_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kb_category_views');
        Schema::dropIfExists('kb_article_views');
    }
} 