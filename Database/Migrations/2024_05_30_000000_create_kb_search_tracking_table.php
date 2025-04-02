<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKbSearchTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Table for tracking searches
        Schema::create('kb_search_queries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mailbox_id')->unsigned();
            $table->string('query', 255);
            $table->integer('results_count')->default(0);
            $table->integer('search_count')->default(1);
            $table->string('locale', 12)->nullable();
            $table->timestamps();
            
            $table->index('mailbox_id');
            $table->index('query');
            $table->index('results_count');
            $table->index('search_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kb_search_queries');
    }
} 