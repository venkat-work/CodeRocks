<?php

namespace App\Http\Controllers\Api\Veterinary\Masters;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Veterinary\Masters\VeterinaryParameterUomM;
use App\Http\Resources\Veterinary\Masters\ParameterUom\parameteruomListResource as parameteruomListResource;
use App\Http\Resources\Veterinary\Masters\ParameterUom\parameteruomEditResource as parameteruomEditResource;
use App\Http\Resources\Veterinary\Masters\ParameterUom\parameteruomdefaultResource as parameteruomdefaultResource;
use Illuminate\Support\Facades\Validator;
use DB;

class ParameterUomController extends Controller
{
    


	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	public function index(Request $request) {
		$filterObj = $this->sortAndFilterOptions($request);
		$data = VeterinaryParameterUomM::where($filterObj['filter'])->orderByRaw($filterObj['sort'])->paginate($filterObj['perPage']);
		return parameteruomListResource::collection($data);
	}

	/**
    *
    * Listing for select boxes
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function list(Request $request){
        $data = VeterinaryParameterUomM::select('id', 'uom_name', 'description')->orderByRaw("id ASC")->get();
        return parameteruomdefaultResource::collection($data);
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
			'uom_name' => 'required|max:50|unique:veterinary_parameter_uom_m',
			'description' => 'required|max:500'
		]);
		if ($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200); 
		$parameterUom = new VeterinaryParameterUomM;
		$parameterUom->uom_name = $request->uom_name;
		$parameterUom->description = $request->description;
		$parameterUom->inserted_by = $authUser->id;
		try{
			DB::beginTransaction();
			$parameterUom->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new parameteruomListResource($parameterUom)], "message" => "Record has been Successfully Inserted"], 201);
	}


	/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

	public function show(Request $request, $id){
		$parameterUom = VeterinaryParameterUomM::findOrFail($id);
		$authUser = $request->user();
		return new parameteruomEditResource($parameterUom);
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
		$parameterUom = VeterinaryParameterUomM::findOrFail($id);
		$validator = Validator::make($request->all(), [
			'uom_name' => 'required|max:50|unique:veterinary_parameter_uom_m,uom_name,'.$id.',id',
			'description' => 'required|max:500'
		]);
		if($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200);
		$parameterUom->uom_name = $request->uom_name;
		$parameterUom->description = $request->description;
		$parameterUom->updated_by = $authUser->id;
		try {
			DB::beginTransaction();
			$parameterUom->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new parameteruomListResource($parameterUom)], "message" => "Record has been Successfully Updated"], 202);
	}


	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function destroy($id){
		$parameterUom = VeterinaryParameterUomM::findOrFail($id);
		$parameterUom->delete();
		return new parameteruomListResource($parameterUom);
	}
}
