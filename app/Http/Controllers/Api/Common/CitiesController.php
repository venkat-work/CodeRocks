<?php

namespace App\Http\Controllers\Api\Common;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Common\CoreCities;
use App\Http\Resources\Common\Cities\citiesListResource as citiesListResource;
use App\Http\Resources\Common\Cities\citiesEditResource as citiesEditResource;
use App\Http\Resources\Common\Cities\citiesdefaultResource as citiesdefaultResource;
use Illuminate\Support\Facades\Validator;
use DB;

class CitiesController extends Controller
{
    


	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	public function index(Request $request) {
		$filterObj = $this->sortAndFilterOptions($request);
		$data = CoreCities::where($filterObj['filter'])->orderByRaw($filterObj['sort'])->paginate($filterObj['perPage']);
		return citiesListResource::collection($data);
	}

	/**
    *
    * Listing for select boxes
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function list($id){
        $data = CoreCities::select('id', 'city_name')->where("state_id", $id)->where("status", "active")->orderByRaw("city_name ASC")->get();
        return citiesdefaultResource::collection($data);
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
			'short_name' => 'required|max:30',
			'city_name' => 'required|max:150',
			'state_id' => 'required|numeric'
		]);
		if ($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200); 
		$cities = new CoreCities;
		$cities->short_name = $request->short_name;
		$cities->city_name = $request->city_name;
		$cities->state_id = $request->state_id;
		$cities->status = 'active';
		$cities->inserted_by = $authUser->id;
		try{
			DB::beginTransaction();
			$cities->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new citiesListResource($cities)], "message" => "Record has been Successfully Inserted"], 201);
	}


	/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

	public function show(Request $request, $id){
		$cities = CoreCities::findOrFail($id);
		$authUser = $request->user();
		return new citiesEditResource($cities);
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
		$cities = CoreCities::findOrFail($id);
		$validator = Validator::make($request->all(), [
			'short_name' => 'required|max:30',
			'city_name' => 'required|max:150',
			'state_id' => 'required|numeric'
		]);
		if($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200);
		$cities->short_name = $request->short_name;
		$cities->city_name = $request->city_name;
		$cities->state_id = $request->state_id;
		$cities->status = $request->status;
		$cities->updated_by = $authUser->id;
		try {
			DB::beginTransaction();
			$cities->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new citiesListResource($cities)], "message" => "Record has been Successfully Updated"], 202);
	}


	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function destroy($id){
		$cities = CoreCities::findOrFail($id);
		$cities->delete();
		return new citiesListResource($cities);
	}
}
