<?php

namespace App\Http\Controllers\Api\Veterinary\Masters;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Veterinary\Masters\VeterinaryParameterTypeM;
use App\Http\Resources\Veterinary\Masters\ParameterType\parametertypeListResource as parametertypeListResource;
use App\Http\Resources\Veterinary\Masters\ParameterType\parametertypeEditResource as parametertypeEditResource;
use App\Http\Resources\Veterinary\Masters\ParameterType\parametertypedefaultResource as parametertypedefaultResource;
use Illuminate\Support\Facades\Validator;
use DB;

class ParameterTypeController extends Controller
{
    


	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	public function index(Request $request) {
		$filterObj = $this->sortAndFilterOptions($request);
		$data = VeterinaryParameterTypeM::where($filterObj['filter'])->orderByRaw($filterObj['sort'])->paginate($filterObj['perPage']);
		return parametertypeListResource::collection($data);
	}

	/**
    *
    * Listing for select boxes
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function list(Request $request){
        $data = VeterinaryParameterTypeM::select('id', 'parameter_name')->orderByRaw("parameter_name ASC")->get();
        return parametertypedefaultResource::collection($data);
    }



	/**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

	public function store(Request $request){
		$authUser = $request->user();
		$validator = Validator::make($request->all(), [
			'parameter_name' => 'required|max:50|unique:veterinary_parameter_type_m',
			'uom_id' => 'required|numeric',
			'description' => 'required|max:500'
		]);
		if ($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200); 
		$parameterType = new VeterinaryParameterTypeM;
		$parameterType->parameter_name = $request->parameter_name;
		$parameterType->uom_id = $request->uom_id;
		$parameterType->description = $request->description;
		$parameterType->inserted_by = $authUser->id;
		try{
			DB::beginTransaction();
			$parameterType->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new parametertypeListResource($parameterType)], "message" => "Record has been Successfully Inserted"], 201);
	}


	/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

	public function show(Request $request, $id){
		$parameterType = VeterinaryParameterTypeM::findOrFail($id);
		$authUser = $request->user();
		return new parametertypeEditResource($parameterType);
	}


	/**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function update(Request $request, $id){
		$authUser = $request->user();
		$parameterType = VeterinaryParameterTypeM::findOrFail($id);
		$validator = Validator::make($request->all(), [
			'parameter_name' => 'required|max:50|unique:veterinary_parameter_type_m,parameter_name,'.$id.',id',
			'uom_id' => 'required|numeric',
			'description' => 'required|max:500'
		]);
		if($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200);
		$parameterType->parameter_name = $request->parameter_name;
		$parameterType->uom_id = $request->uom_id;
		$parameterType->description = $request->description;
		$parameterType->updated_by = $authUser->id;
		try {
			DB::beginTransaction();
			$parameterType->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new parametertypeListResource($parameterType)], "message" => "Record has been Successfully Updated"], 202);
	}


	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function destroy($id){
		$parameterType = VeterinaryParameterTypeM::findOrFail($id);
		$parameterType->delete();
		return new parametertypeListResource($parameterType);
	}
}
