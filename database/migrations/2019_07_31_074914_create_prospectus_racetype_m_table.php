<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProspectusRacetypeMTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prospectus_racetype_m', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('racetype_name','100')->unique();
			$table->text('racetype_description');
			$table->string('status','20');
			$table->integer('inserted_by');
			$table->integer('updated_by')->nullable();
			$table->index(["racetype_name"]);
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
        Schema::dropIfExists('prospectus_racetype_m');
    }
}
