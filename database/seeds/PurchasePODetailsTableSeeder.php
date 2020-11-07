<?php

use Illuminate\Database\Seeder;

class PurchasePODetailsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Models\Purchase\Transactions\PurchasePoDetails::truncate();
		$dataObj = [
			[
				'po_id' => 'Po Id 1',
				'vendor_id' => 'Vendor Id 1',
				'remarks' => 'Remarks 1',
				'quotation_id' => 'Quotation Id 1',
				'indent_category_id' => 'Indent Category Id 1',
				'indent_sub_category_id' => 'Indent Sub Category Id 1',
				'indent_material_id' => 'Indent Material Id 1',
				'indent_quantity' => 'Indent Quantity 1',
				'unit_rate' => 'Unit Rate 1',
				'sgst_percentage' => 'Sgst Percentage 1',
				'sgst_amount' => 'Sgst Amount 1',
				'cgst_percentage' => 'Cgst Percentage 1',
				'cgst_amount' => 'Cgst Amount 1',
				'igst_percentage' => 'Igst Percentage 1',
				'igst_amount' => 'Igst Amount 1',
				'cess_percentage' => 'Cess Percentage 1',
				'cess_amount' => 'Cess Amount 1',
				'other_charges' => 'Other Charges 1',
				'total_amount' => 'Total Amount 1',
				'status' => 'active',
				'inserted_by' => '1',
				'updated_by' => '1'
			],
			[
				'po_id' => 'Po Id 2',
				'vendor_id' => 'Vendor Id 2',
				'remarks' => 'Remarks 2',
				'quotation_id' => 'Quotation Id 2',
				'indent_category_id' => 'Indent Category Id 2',
				'indent_sub_category_id' => 'Indent Sub Category Id 2',
				'indent_material_id' => 'Indent Material Id 2',
				'indent_quantity' => 'Indent Quantity 2',
				'unit_rate' => 'Unit Rate 2',
				'sgst_percentage' => 'Sgst Percentage 2',
				'sgst_amount' => 'Sgst Amount 2',
				'cgst_percentage' => 'Cgst Percentage 2',
				'cgst_amount' => 'Cgst Amount 2',
				'igst_percentage' => 'Igst Percentage 2',
				'igst_amount' => 'Igst Amount 2',
				'cess_percentage' => 'Cess Percentage 2',
				'cess_amount' => 'Cess Amount 2',
				'other_charges' => 'Other Charges 2',
				'total_amount' => 'Total Amount 2',
				'status' => 'active',
				'inserted_by' => '1',
				'updated_by' => '1'
			],
			[
				'po_id' => 'Po Id 3',
				'vendor_id' => 'Vendor Id 3',
				'remarks' => 'Remarks 3',
				'quotation_id' => 'Quotation Id 3',
				'indent_category_id' => 'Indent Category Id 3',
				'indent_sub_category_id' => 'Indent Sub Category Id 3',
				'indent_material_id' => 'Indent Material Id 3',
				'indent_quantity' => 'Indent Quantity 3',
				'unit_rate' => 'Unit Rate 3',
				'sgst_percentage' => 'Sgst Percentage 3',
				'sgst_amount' => 'Sgst Amount 3',
				'cgst_percentage' => 'Cgst Percentage 3',
				'cgst_amount' => 'Cgst Amount 3',
				'igst_percentage' => 'Igst Percentage 3',
				'igst_amount' => 'Igst Amount 3',
				'cess_percentage' => 'Cess Percentage 3',
				'cess_amount' => 'Cess Amount 3',
				'other_charges' => 'Other Charges 3',
				'total_amount' => 'Total Amount 3',
				'status' => 'active',
				'inserted_by' => '1',
				'updated_by' => '1'
			]
		];
		for($i=0; $i< count($dataObj); $i++){
			App\Models\Purchase\Transactions\PurchasePoDetails::create($dataObj[$i]);
		}
    }
}
