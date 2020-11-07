<?php

function terminal($command)
{
	
	if(function_exists('system')){
		//system
		ob_start();
		system($command , $return_var);
		$output = ob_get_contents();
		ob_end_clean();
	} else if(function_exists('passthru')){
		//passthru
		ob_start();
		passthru($command , $return_var);
		$output = ob_get_contents();
		ob_end_clean();
	} else if(function_exists('exec')){
		//exec
		exec($command , $output , $return_var);
		$output = implode("n" , $output);
	} else if(function_exists('shell_exec')){
		//shell_exec
		$output = shell_exec($command) ;
	} else {
		$output = 'Command execution not possible on this system';
		$return_var = 1;
	}
	
	return array('output' => $output , 'status' => $return_var);
}


function migrationString($columns){
	$str = '';
	$indexList = array();

	foreach($columns as $key => $val){
		if(!isset($val['column'])){
			echo "column name: is missing";exit;
		}

		if(!isset($val['type'])){
			echo "column: ".$val['column']." type: is missing"; exit;
		}
		if($val['type'] == 'integer'){
			$column = trim($val['column']);
			
			$str .= "\n".'			$table->integer(\''.$column.'\')';
			
			if(isset($val['null']) && $val['null'] == "true"){
				$str .= '->nullable()';
			}

			if(isset($val['index']) && $val['index'] == "true"){
				$indexList[] = $column;
			}

			if(isset($val['unique']) && $val['unique'] == "true"){
				$str .= '->unique()';
			}
			$str .= ";";
		} else if($val['type'] == 'string'){
			$size = 200;
			$column = trim($val['column']);
			
			if(isset($val['size']) && trim($val['size']) != "")
				$size = (int) trim($val['size']);
			$str .= "\n".'			$table->string(\''.$column.'\',\''.$size.'\')';
			
			if(isset($val['null']) && $val['null'] == "true"){
				$str .= '->nullable()';
			}

			if(isset($val['index']) && $val['index'] == "true"){
				$indexList[] = $column;
			}

			if(isset($val['unique']) && $val['unique'] == "true"){
				$str .= '->unique()';
			}
			$str .= ";";
		} else if($val['type'] == 'text'){
			$column = trim($val['column']);
			
			$str .= "\n".'			$table->text(\''.$column.'\')';
			
			if(isset($val['null']) && $val['null'] == "true"){
				$str .= '->nullable()';
			}

			if(isset($val['index']) && $val['index'] == "true"){
				$indexList[] = $column;
			}

			if(isset($val['unique']) && $val['unique'] == "true"){
				$str .= '->unique()';
			}
			$str .= ";";
		} else if($val['type'] == 'double'){
			$column = trim($val['column']);
			
			$str .= "\n".'			$table->double(\''.$column.'\', 13, 2)';
			
			if(isset($val['null']) && $val['null'] == "true"){
				$str .= '->nullable()';
			}

			if(isset($val['index']) && $val['index'] == "true"){
				$indexList[] = $column;
			}

			if(isset($val['unique']) && $val['unique'] == "true"){
				$str .= '->unique()';
			}
			$str .= ";";
		} else if($val['type'] == 'enum'){
			$column = trim($val['column']);
			$size = ['active'];
			
			if(isset($val['size']) && trim($val['size']) != "")
				$size = $val['size'];

			$str .= "\n".'			$table->enum(\''.$column.'\','.$size.')';
			
			if(isset($val['null']) && $val['null'] == "true"){
				$str .= '->nullable()';
			}

			if(isset($val['index']) && $val['index'] == "true"){
				$indexList[] = $column;
			}

			if(isset($val['unique']) && $val['unique'] == "true"){
				$str .= '->unique()';
			}
			$str .= ";";
		} else {
			$column = trim($val['column']);
			
			$str .= "\n".'			$table->'.$val['type'].'(\''.$column.'\')';
			
			if(isset($val['null']) && $val['null'] == "true"){
				$str .= '->nullable()';
			}

			if(isset($val['index']) && $val['index'] == "true"){
				$indexList[] = $column;
			}

			if(isset($val['unique']) && $val['unique'] == "true"){
				$str .= '->unique()';
			}
			$str .= ";";
		} 
	}

	if(count($indexList) > 0){
		foreach($indexList as $key => $val){
			$str .="\n".'			$table->index([\''.$val.'\']);';
		}
	}
	return $str;
}

function generateController($path, $model, $listResource, $editResource, $validations, $editvalidations, $fields){
	$temp = explode("\\", $path);
	$objName = $temp[count($temp) - 1];
	$objName = lcfirst($objName);

	$str = '';
//List action
	$str .= "\n\n\n".'	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */';

	$str .= "\n\n".'	public function index(Request $request) {';
	$str .= "\n".'		$filterObj = $this->sortAndFilterOptions($request);';
    $str .= "\n".'		$data = '.$model.'::where($filterObj[\'filter\'])->orderByRaw($filterObj[\'sort\'])->paginate($filterObj[\'perPage\']);';
    $str .= "\n".'		return '.$listResource.'::collection($data);';
    $str .= "\n".'	}';

//Store action
   	$str .= "\n\n\n".'	/**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */';
    $str .= "\n\n".'	public function store(Request $request){';
    $str .= "\n".'		$authUser = $request->user();';
    
    if($validations != ""){
	    $str .= "\n".'		$validator = Validator::make($request->all(), [';
	    $str .= "\n".$validations;
	    $str .= "\n".'		]);';

	   	$str .= "\n".'		if ($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200); ';
   	}
    $str .= "\n".'		$'.$objName.' = new '.$model.';';
    foreach($fields as $key => $val){    	
    	if($val == "status")
    		$str .= "\n".'		$'.$objName.'->status = \'active\';';
    	else if($val == "inserted_by")
    		$str .= "\n".'		$'.$objName.'->inserted_by = $authUser->id;';
    	else if($val == "updated_by") {}
    	else
    		$str .= "\n".'		$'.$objName.'->'.$val.' = $request->'.$val.';';
	}
    $str .= "\n".'		try{';
    $str .= "\n".'			DB::beginTransaction();';
	$str .= "\n".'			$'.$objName.'->save();';
    $str .= "\n".'			DB::commit();';
	$str .= "\n".'		} catch(Exception $e){';       
    $str .= "\n".'			DB::rollback();';
	$str .= "\n".'		}';
	$str .= "\n".'		return response()->json(["success" => ["data" => new '.$listResource.'($'.$objName.')], "message" => "Record has been Successfully Inserted"], 201);';
    $str .= "\n".'	}';

//Get one action
    
    $str .="\n\n\n".'	/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */';

    $str .="\n\n".'	public function show(Request $request, $id){';
    $str .="\n".'		$'.$objName.' = '.$model.'::findOrFail($id);';
    $str .="\n".'		$authUser = $request->user();';
    $str .="\n".'		return new '.$editResource.'($'.$objName.');';
    $str .="\n".'	}';

    $str .="\n\n\n".'	/**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */';
    $str .= "\n".'	public function update(Request $request, $id){';
    $str .= "\n".'		$authUser = $request->user();';
    $str .= "\n".'		$'.$objName.' = '.$model.'::findOrFail($id);';
    if($editvalidations != ""){
    	$str .= "\n".'		$validator = Validator::make($request->all(), [';
    	$str .= "\n".$editvalidations;
    	$str .= "\n".'		]);';
    	$str .= "\n".'		if($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200);';
	}

    foreach($fields as $key => $val){
    	if($val == "updated_by")
    		$str .= "\n".'		$'.$objName.'->updated_by = $authUser->id;';
    	else if($val == "inserted_by") {}
    	else
    		$str .= "\n".'		$'.$objName.'->'.$val.' = $request->'.$val.';';
	}
	
	$str .= "\n".'		try {';
   	$str .= "\n".'			DB::beginTransaction();';
    $str .= "\n".'			$'.$objName.'->save();';
    $str .= "\n".'			DB::commit();';
	$str .= "\n".'		} catch(Exception $e){';       
    $str .= "\n".'			DB::rollback();';
	$str .= "\n".'		}';
    $str .= "\n".'		return response()->json(["success" => ["data" => new '.$listResource.'($'.$objName.')], "message" => "Record has been Successfully Updated"], 202);';
    $str .= "\n".'	}';


    $str .="\n\n\n".'	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */';
    $str .= "\n".'	public function destroy($id){';
    $str .= "\n".'		$'.$objName.' = '.$model.'::findOrFail($id);';
    $str .= "\n".'		$'.$objName.'->delete();';
    $str .= "\n".'		return new '.$listResource.'($'.$objName.');';
    $str .= "\n".'	}';

    return $str;
}