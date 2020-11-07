<?php

use Illuminate\Database\Seeder;

class ProspectusBackupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Models\Prospectus\Transactions\ProspectusBackup::truncate();
		$dataObj = [
			[
				'race_year' => 'Race Year 1',
				'race_season' => 'Race Season 1',
				'data' => 'Data 1',
				'inserted_by' => '1',
				'updated_by' => '1'
			],
			[
				'race_year' => 'Race Year 2',
				'race_season' => 'Race Season 2',
				'data' => 'Data 2',
				'inserted_by' => '1',
				'updated_by' => '1'
			],
			[
				'race_year' => 'Race Year 3',
				'race_season' => 'Race Season 3',
				'data' => 'Data 3',
				'inserted_by' => '1',
				'updated_by' => '1'
			]
		];
		for($i=0; $i< count($dataObj); $i++){
			App\Models\Prospectus\Transactions\ProspectusBackup::create($dataObj[$i]);
		}
    }
}
