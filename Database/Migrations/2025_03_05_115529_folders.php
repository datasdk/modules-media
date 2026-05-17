<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Kalnoy\Nestedset\NestedSet;

class Folders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if(!Schema::hasTable("folders"))
        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Opret kolonner til nested set (lft, rgt, depth, parent_id m.v.)
            NestedSet::columns($table);
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
        if(Schema::hasTable("folders"))
        Schema::dropIfExists('folders');
    }
}
