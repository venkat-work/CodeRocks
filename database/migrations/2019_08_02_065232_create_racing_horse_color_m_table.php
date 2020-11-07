<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRacingHorseColorMTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('racing_horse_color_m', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('horse_color_name','200')->unique();
			$table->text('horse_color_description');
			$table->string('status','20');
			$table->integer('inserted_by');
			$table->integer('updated_by')->nullable();
			$table->index(["horse_color_name"]);
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
        Schema::dropIfExists('racing_horse_color_m');
    }
}
