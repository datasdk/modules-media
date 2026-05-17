<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MediaSeen extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        if(!Schema::hasTable('media_seen'))
        Schema::create('media_seen', function (Blueprint $table) {

			$table->increments('id');
			$table->integer('media_id')->unsigned();
            $table->integer('user_id')->unsigned();
			$table->date('seen')->nullable();
			$table->integer('seen_amount')->default(0);
			$table->integer('seen_prc')->default(0);
            $table->integer('sorting')->default(0);
            $table->softDeletes();
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

        Schema::dropIfExists('media_seen');

    }
}


