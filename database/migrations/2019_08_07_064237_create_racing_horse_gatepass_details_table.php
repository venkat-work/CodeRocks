<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRacingHorseGatepassDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('racing_horse_gatepass_details', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->integer('horse_id');
			$table->integer('trainer_id');
            //$table->integer('igp_date');
			$table->string('gatepass_type','1');
			$table->string('going_to','200')->nullable();
			$table->string('arriving_from','100')->nullable();
			$table->date('requested_date')->nullable();
			$table->dateTime('gatepass_gen_date')->nullable();
			$table->dateTime('arrival_outgoing_date')->nullable();
			$table->string('remarks','500')->nullable();
            $table->string('security_remarks','500')->nullable();
			$table->integer('is_vet_approved')->nullable();
			$table->string('vet_remarks','500')->nullable();
            $table->dateTime('vet_approved_date')->nullable();

            $table->dateTime('accounts_approved_date')->nullable();
            $table->integer('is_accounts_approved')->nullable();
            $table->integer('accounts_remarks', '500')->nullable();

            $table->string('submit_from',20);
            $table->string('status', 20);
			$table->integer('inserted_by');
			$table->integer('updated_by')->nullable();
            
			$table->index(["horse_id"]);
            $table->index(["trainer_id"]);
            $table->index(["gatepass_type"]);
            $table->index(["requested_date"]);
            $table->index(["gatepass_gen_date"]);
            $table->index(["arrival_outgoing_date"]);
            $table->index(["is_vet_approved"]);
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
        Schema::dropIfExists('racing_horse_gatepass_details');
    }
}
