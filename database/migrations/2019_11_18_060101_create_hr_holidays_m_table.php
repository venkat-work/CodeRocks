<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHrHolidaysMTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_holidays_m', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('fin_year','10');
			$table->date('holiday_date');
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
        Schema::dropIfExists('hr_holidays_m');
    }
}
