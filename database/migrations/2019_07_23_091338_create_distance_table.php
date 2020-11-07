<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prospectus_distance_m', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('distance')->unique();
            $table->string('description');

            $table->string('status');
            //$table->string('workflow_id')->nullable();

            $table->integer('inserted_by')->nullable();
            $table->integer('updated_by')->nullable();

            //$table->integer('verified_by')->nullable();
            //$table->date('verified_date')->nullable();

            //$table->integer('approved_by')->nullable();
            //$table->date('approved_date')->nullable();

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
        Schema::dropIfExists('distance');
    }
}
