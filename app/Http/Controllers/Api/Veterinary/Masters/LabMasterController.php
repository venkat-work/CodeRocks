<?php

namespace App\Http\Controllers\Api\Veterinary\Masters;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Veterinary\Masters\VeterinaryLabsM;
use App\Http\Resources\Veterinary\Masters\LabMaster\labmasterListResource as labmasterListResource;
use App\Http\Resources\Veterinary\Masters\LabMaster\labmasterEditResource as labmasterEditResource;
use App\Http\Resources\Veterinary\Masters\LabMaster\labmasterdefaultResource as labmasterdefaultResource;
use Illuminate\Support\Facades\Validator;
use DB;

class LabMasterController extends Controller
{
    


	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	public function index(Request $request) {
		$filterObj = $this->sortAndFilterOptions($request);
		$data = VeterinaryLabsM::where($filterObj['filter'])->orderByRaw($filterObj['sort'])->paginate($filterObj['perPage']);
		return labmasterListResource::collection($data);
	}

	/**
    *
    * Listing for select boxes
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function list(Request $request){
        $data = VeterinaryLabsM::select('id', 'lab_name')->orderByRaw("lab_name ASC")->get();
        return labmasterdefaultResource::collection($data);
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
			'lab_name' => 'required|max:50|unique:veterinary_labs_m',
			'description' => 'required|max:500'
		]);
		if ($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200); 
		$labMaster = new VeterinaryLabsM;
		$labMaster->lab_name = $request->lab_name;
		$labMaster->description = $request->description;
		$labMaster->inserted_by = $authUser->id;
		try{
			DB::beginTransaction();
			$labMaster->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new labmasterListResource($labMaster)], "message" => "Record has been Successfully Inserted"], 201);
	}


	/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

	public function show(Request $request, $id){
		$labMaster = VeterinaryLabsM::findOrFail($id);
		$authUser = $request->user();
		return new labmasterEditResource($labMaster);
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
		$labMaster = VeterinaryLabsM::findOrFail($id);
		$validator = Validator::make($request->all(), [
			'lab_name' => 'required|max:50|unique:veterinary_labs_m,lab_name,'.$id.',id',
			'description' => 'required|max:500'
		]);
		if($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200);
		$labMaster->lab_name = $request->lab_name;
		$labMaster->description = $request->description;
		$labMaster->updated_by = $authUser->id;
		try {
			DB::beginTransaction();
			$labMaster->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new labmasterListResource($labMaster)], "message" => "Record has been Successfully Updated"], 202);
	}


	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function destroy($id){
		$labMaster = VeterinaryLabsM::findOrFail($id);
		$labMaster->delete();
		return new labmasterListResource($labMaster);
	}
}
