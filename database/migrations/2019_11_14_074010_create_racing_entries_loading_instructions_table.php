<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRacingEntriesLoadingInstructionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('racing_entries_loading_instructions', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->bigInteger('division_id');
			$table->date('race_date');
            $table->bigInteger('horse_id');
			$table->bigInteger('old_instruction')->nullable();
			$table->bigInteger('new_instruction');
			$table->integer('inserted_by');
			$table->integer('updated_by')->nullable();
			$table->index(['division_id']);
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
        Schema::dropIfExists('racing_entries_loading_instructions');
    }
}
