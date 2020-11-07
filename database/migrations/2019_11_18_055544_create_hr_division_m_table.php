<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHrDivisionMTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_division_m', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->integer('department_id');
			$table->string('division_name','50');
			$table->string('description','500')->nullable();
			$table->integer('inserted_by');
			$table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('hr_division_m');
    }
}
