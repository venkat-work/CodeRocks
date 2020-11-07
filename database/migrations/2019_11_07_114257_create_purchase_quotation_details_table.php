<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseQuotationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_quotation_details', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->integer('quotation_id');
			$table->integer('indent_id');
			$table->integer('indent_category_id');
			$table->integer('indent_sub_category_id');
			$table->integer('indent_material_id');
			$table->integer('indent_quantity');
			$table->double('unit_rate','13','5');
			$table->double('sgst_percentage','13','2');
			$table->double('sgst_amount','13','2');
			$table->double('cgst_percentage','13','2');
			$table->double('cgst_amount','13','2');
			$table->double('igst_percentage','13','2');
			$table->double('igst_amount','13','2');
			$table->double('cess_percentage','13','2');
			$table->double('cess_amount','13','2');
			$table->double('other_charges','13','2')->nullable();
			$table->double('total_amount','13','2');
			$table->string('remarks','200')->nullable();
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
        Schema::dropIfExists('purchase_quotation_details');
    }
}
