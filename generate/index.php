<?php
	
	require_once("func.php");
	$path = "";
	$table = "";
	$columnsList = array();

	if(isset($argv[1]) && trim($argv[1]) != ""){
		$path = trim($argv[1]); //path Ex: Prospectus/Masters/Distance
		$path = explode("/", $path);
		for($i=0;$i<count($path);$i++){
			if(($i+1) != count($path)){
				$path[$i] = ucwords($path[$i]);
			}
		}
		$path = implode("\\",$path);
	}

	if($path == "help"){
		echo "path_of_controller table_name column:'column_name',type:'string'|integer|text,size:'200'|optional,null:'true'|optional,index:'true'|optional,unique:'true'|optional";exit;
	}

	if(isset($argv[2]) && trim($argv[2]) != "")
		$table = trim($argv[2]);

	if(isset($argv[3]) && trim($argv[3]) != ""){
		$columnsList = array();

		for($i=3;$i < count($argv);$i++){
			$temp = explode(",", $argv[$i]);
			$t = array();
			foreach($temp as $k => $v){
				$fields = explode(":", $v);
				if(count($fields) == 2){
					$t[$fields[0]] = $fields[1];
				} else {
					$t[$fields[0]] = true;
				}
			}
			$columnsList[] = $t;
		}
	}

	if($path == "" || $table == ""){
		echo "index.php PATH_CONTROLLER TABLE_NAME";exit;
	}
	$rootPath = getcwd()."/";

	$temp = explode("\\", $path);
	$objName = $temp[count($temp) - 1];

	//Migration created
	$output = terminal("php artisan make:migration create_".$table."_table");
	
	if($output['status'] == 0){
		$migrationFilePath = "database/migrations/";
		$migrationFile = trim(str_replace("Created Migration:", "", $output['output']));
		$fileContent = file_get_contents($rootPath.$migrationFilePath.$migrationFile.".php");
		$str = '$table->bigIncrements(\'id\');';
		$str .= migrationString($columnsList);
		//$str .= "\n".'			$table->integer(\'race_type_name\')->unique();';
		$fileContent = str_replace('$table->bigIncrements(\'id\');', $str, $fileContent);
		file_put_contents($rootPath.$migrationFilePath.$migrationFile.".php", $fileContent);
	} else {
		echo "Migration file is not created";exit;
	}

	//Model creating
	$model = str_replace(" ","",ucwords(strtolower(str_replace("_"," ",$table))));
	$modelPath = $temp;
	$modelPath[count($modelPath) - 1] = $model;
	$modelPath = implode("\\",$modelPath);
	$output = terminal('php artisan make:model Models\\\\'.str_replace('\\', '\\\\', $modelPath));
	if($output['status'] == 0){
		$contFilePath = "app/Models/";
		$cont = str_replace('\\', '/', str_replace($objName,"",$path).$model.".php");
		$fileContent = file_get_contents($rootPath.$contFilePath.$cont);
		$fileContent = str_replace('//', 'protected $table = \''.$table.'\';', $fileContent);
		file_put_contents($rootPath.$contFilePath.$cont, $fileContent);
	} else {
		echo "Model not created";exit;
	} 
	
	$fullList = "";
	$manList = "";
	$validations = "";
	$editvalidations = "";
	$fields = array();

	foreach($columnsList as $key => $val){
		if(!isset($val['null']) || $val['null'] == "false"){
			if($manList != "")
				$manList .= ",\n";
			$manList .= '			\''.$val['column'].'\' => $this->'.$val['column'];


			if($val['column'] == 'inserted_by' || $val['column'] == 'updated_by' || $val['column'] == 'status'){

			} else {
				if($validations != "")
					$validations .= ",\n";
				$validations .= '			\''.$val['column'].'\' => \'required'; 

				if($val['type'] == 'string'){
					if(!isset($val['size']))
						$val['size'] = 200;
					$validations .= '|max:'.$val['size'];
				}

				if($val['type'] == 'integer'){
					$validations .= '|numeric';
				}

				if(isset($val['unique']) && $val['unique'] == "true"){
					$validations .= '|unique:'.$table;
				}

				$validations .= '\'';

				//Edit validations
				if($editvalidations != "")
					$editvalidations .= ",\n";
				$editvalidations .= '			\''.$val['column'].'\' => \'required'; 

				if($val['type'] == 'string'){
					if(!isset($val['size']))
						$val['size'] = 200;
					$editvalidations .= '|max:'.$val['size'];
				}

				if($val['type'] == 'integer'){
					$editvalidations .= '|numeric';
				}

				if(isset($val['unique']) && $val['unique'] == "true"){
					$editvalidations .= '|unique:'.$table.','.$val['column'].',\'.$id.\',id';
				}

				$editvalidations .= '\'';
			}
		}

		if($fullList != "")
				$fullList .= ",\n";
			$fullList .= '			\''.$val['column'].'\' => $this->'.$val['column'];

		//if($val['column'] == 'inserted_by' || $val['column'] == 'updated_by' || $val['column'] == 'status'){
			$fields[] = $val['column'];
		//}
	}

	$fullList .= ",\n".'			\'updated_at\' => date(config(\'custom.datetime\'), strtotime($this->updated_at))';
	if($manList == ""){
		$fullList = $manList;
	}
	$fullList = 'return ['."\n".'			\'id\' => $this->id,'."\n".$fullList."\n".'		]';
	$manList = 'return ['."\n".'			\'id\' => $this->id,'."\n".$manList."\n".'		]';

	//Resources creating
	$listResource = strtolower($objName)."ListResource";
	$editResource = strtolower($objName)."EditResource";
	$defaultResource = strtolower($objName)."defaultResource";

	$output = terminal('php artisan make:resource '.str_replace('\\', '\\\\', $path.'\\'.$defaultResource));
	if($output['status'] == 0){

	} else {
		echo "Default resource is not created";exit;
	}
	$output = terminal('php artisan make:resource '.str_replace('\\', '\\\\', $path.'\\'.$listResource));
	if($output['status'] == 0){
		$contFilePath = "app/Http/Resources/";
		$cont = str_replace('\\', '/', $path."/".$listResource.".php");
		$fileContent = file_get_contents($rootPath.$contFilePath.$cont);
		$fileContent = str_replace('return parent::toArray($request)', $fullList, $fileContent);
		file_put_contents($rootPath.$contFilePath.$cont, $fileContent);

	} else {
		echo "Resource List not created";exit;
	}

	$output = terminal('php artisan make:resource '.str_replace('\\', '\\\\', $path.'\\'.$editResource));
	if($output['status'] == 0){
		$contFilePath = "app/Http/Resources/";
		$cont = str_replace('\\', '/', $path."/".$editResource.".php");
		$fileContent = file_get_contents($rootPath.$contFilePath.$cont);
		$fileContent = str_replace('return parent::toArray($request)', $fullList, $fileContent);
		file_put_contents($rootPath.$contFilePath.$cont, $fileContent);
	} else {
		echo "Resource Edit not created";exit;
	}
	//Controller creating

	$output = terminal('php artisan make:controller Api\\\\'.str_replace('\\', '\\\\', $path."Controller"));
	if($output['status'] == 0){
		$contFilePath = "app/Http/Controllers/Api/";
		$cont = str_replace('\\', '/', $path."Controller.php");

		$headersSearch = 'use App\Http\Controllers\Controller;';
		$headers = "\n".'use App\Models\\'.$modelPath.';';
		$headers .= "\n".'use App\Http\Resources\\'.$path.'\\'.$listResource.' as '.$listResource.';';
		$headers .= "\n".'use App\Http\Resources\\'.$path.'\\'.$editResource.' as '.$editResource.';';
		$headers .= "\n".'use App\Http\Resources\\'.$path.'\\'.$defaultResource.' as '.$defaultResource.';';
		$headers .= "\n".'use Illuminate\Support\Facades\Validator;';
		$headers .= "\n".'use DB;';


		$data = generateController($path, $model, $listResource, $editResource, $validations, $editvalidations, $fields);
		$fileContent = file_get_contents($rootPath.$contFilePath.$cont);
		$fileContent = str_replace($headersSearch, $headersSearch.$headers, $fileContent);
		$fileContent = str_replace("//", $data, $fileContent);
		file_put_contents($rootPath.$contFilePath.$cont, $fileContent);
	} else {
		echo "Controller not created";exit;
	}

	//Seeder creating

	$output = terminal('php artisan make:seeder '.$objName."TableSeeder");
	if($output['status'] == 0){
		$contFilePath = "database/seeds/";
		$cont = $objName."TableSeeder.php";

		$modelObj =  str_replace($objName,"",$path).$model;
		$data = 'App\Models\\'.$modelObj.'::truncate();';

		$str = "";
		for($i=0;$i<3;$i++){
			if($str != "")
				$str .= ",";

			$str .= "\n".'			[';
			$str2 = "";

			foreach($fields as $key => $val){
				if($str2 != "")
					$str2 .= ",";
				if($val == "updated_by"){
					$str2 .= "\n				'".$val.'\' => \'1\'';
				} else if($val == "inserted_by"){
					$str2 .= "\n				'".$val.'\' => \'1\'';
				} else if($val == "status"){
					$str2 .= "\n				'".$val.'\' => \'active\'';
				} else {
					$str2 .= "\n				'".$val.'\' => \''.ucwords(strtolower(str_replace("_"," ",$val)))." ".($i+1).'\'';
				}
			}

			$str .= $str2;
			$str .= "\n".'			]';
		}
		$data .= "\n".'		$dataObj = [';
		$data .= $str;
		$data .= "\n".'		];';

		$data .= "\n".'		for($i=0; $i< count($dataObj); $i++){';
    	$data .= "\n".'			App\Models\\'.$modelObj.'::create($dataObj[$i]);';
    	$data .= "\n".'		}';

		$fileContent = file_get_contents($rootPath.$contFilePath.$cont);
		$fileContent = str_replace("//", $data, $fileContent);
		file_put_contents($rootPath.$contFilePath.$cont, $fileContent);

		//including the file to main seeder file
		$cont = "DatabaseSeeder.php";
		$fileContent = file_get_contents($rootPath.$contFilePath.$cont);
		$fileContent = str_replace("//AddNew//", '$this->call('.$objName.'TableSeeder::class);'."\n".'		//AddNew//', $fileContent);
		file_put_contents($rootPath.$contFilePath.$cont, $fileContent);

		echo "\nSeeder data to generate run this command: php artisan db:seed --class=".$objName."TableSeeder\n\n";
	} else {

	}

	echo 'Migrate Database command: php artisan migrate'."\n\n";

	echo 'Copy into api.php: Route::resource(\''.strtolower($objName).'\', \'Api\\'.$path."Controller".'\');'."\n";
?>