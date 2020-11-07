<?php

use Illuminate\Database\Seeder;

class OwnertypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Models\Racing\Masters\RacingOwnerTypesM::truncate();
		$dataObj = [
			[
				'owner_type_name' => 'Individual',
				'owner_type_description' => 'Individual Owner',
				'status' => 'active',
				'inserted_by' => '1',
				'updated_by' => '1'
			],
			[
				'owner_type_name' => 'Syndicate',
				'owner_type_description' => 'Syndicate Owner',
				'status' => 'active',
				'inserted_by' => '1',
				'updated_by' => '1'
			],
			[
				'owner_type_name' => 'Private Limited',
				'owner_type_description' => 'Private Limited',
				'status' => 'active',
				'inserted_by' => '1',
				'updated_by' => '1'
			]
		];
		for($i=0; $i< count($dataObj); $i++){
			App\Models\Racing\Masters\RacingOwnerTypesM::create($dataObj[$i]);
		}
    }
}
