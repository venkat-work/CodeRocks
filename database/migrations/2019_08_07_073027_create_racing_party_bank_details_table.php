<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRacingPartyBankDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('racing_party_bank_details', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->bigInteger('party_registration_id')->unsigned();
			$table->string('account_number','20');
			$table->string('bank_name','100');
			$table->string('branch','100');
            $table->string('ifsc_code', 20)->nullable();
			$table->integer('country_id');
			$table->integer('state_id');
			$table->integer('city_id');
			$table->string('pincode','10')->nullable();
			$table->string('address','100')->nullable();
			$table->integer('inserted_by');
			$table->integer('updated_by')->nullable();
			$table->index(["party_registration_id"]);
            $table->index(["country_id"]);
            $table->index(["state_id"]);
            $table->index(["city_id"]);
            $table->timestamps();

            $table->foreign('party_registration_id')->references('id')->on('racing_party_registrations')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('racing_party_bank_details');
    }
}
