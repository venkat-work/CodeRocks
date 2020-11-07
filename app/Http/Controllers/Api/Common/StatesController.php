<?php

namespace App\Http\Controllers\Api\Common;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Common\CoreStates;
use App\Http\Resources\Common\States\statesListResource as statesListResource;
use App\Http\Resources\Common\States\statesEditResource as statesEditResource;
use App\Http\Resources\Common\States\statesdefaultResource as statesdefaultResource;
use Illuminate\Support\Facades\Validator;
use DB;

class StatesController extends Controller
{
    


	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	public function index(Request $request) {
		$filterObj = $this->sortAndFilterOptions($request);
		$data = CoreStates::where($filterObj['filter'])->orderByRaw($filterObj['sort'])->paginate($filterObj['perPage']);
		return statesListResource::collection($data);
	}

	/**
    *
    * Listing for select boxes
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function list($id){
        $data = CoreStates::select('id', 'state_name')->where("country_id", $id)->where("status", "active")->orderByRaw("state_name ASC")->get();
        return statesdefaultResource::collection($data);
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
			'short_name' => 'required|max:50',
			'state_name' => 'required|max:150',
			'country_id' => 'required|numeric'
		]);
		if ($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200); 
		$states = new CoreStates;
		$states->short_name = $request->short_name;
		$states->state_name = $request->state_name;
		$states->country_id = $request->country_id;
		$states->status = 'active';
		$states->inserted_by = $authUser->id;
		try{
			DB::beginTransaction();
			$states->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new statesListResource($states)], "message" => "Record has been Successfully Inserted"], 201);
	}


	/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

	public function show(Request $request, $id){
		$states = CoreStates::findOrFail($id);
		$authUser = $request->user();
		return new statesEditResource($states);
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
		$states = CoreStates::findOrFail($id);
		$validator = Validator::make($request->all(), [
			'short_name' => 'required|max:50',
			'state_name' => 'required|max:150',
			'country_id' => 'required|numeric'
		]);
		if($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200);
		$states->short_name = $request->short_name;
		$states->state_name = $request->state_name;
		$states->country_id = $request->country_id;
		$states->status = $request->status;
		$states->updated_by = $authUser->id;
		try {
			DB::beginTransaction();
			$states->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new statesListResource($states)], "message" => "Record has been Successfully Updated"], 202);
	}


	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function destroy($id){
		$states = CoreStates::findOrFail($id);
		$states->delete();
		return new statesListResource($states);
	}
}
