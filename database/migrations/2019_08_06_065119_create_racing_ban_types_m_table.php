<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRacingBanTypesMTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('racing_ban_types_m', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('ban_type_name','200')->unique();
			$table->string('ban_type_description','200');
			$table->string('status','20');
			$table->integer('inserted_by');
			$table->integer('updated_by')->nullable();
			$table->index(["ban_type_name"]);
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
        Schema::dropIfExists('racing_ban_types_m');
    }
}
