<?php

namespace App\Http\Controllers\Api\Common;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Common\CoreCountry;
use App\Http\Resources\Common\Countries\countriesListResource as countriesListResource;
use App\Http\Resources\Common\Countries\countriesEditResource as countriesEditResource;
use App\Http\Resources\Common\Countries\countriesdefaultResource as countriesdefaultResource;
use Illuminate\Support\Facades\Validator;
use DB;

class CountriesController extends Controller
{
    


	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	public function index(Request $request) {
		$filterObj = $this->sortAndFilterOptions($request);
		$data = CoreCountry::where($filterObj['filter'])->orderByRaw($filterObj['sort'])->paginate($filterObj['perPage']);
		return countriesListResource::collection($data);
	}

	/**
    *
    * Listing for select boxes
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function list(Request $request){
        $data = CoreCountry::select('id', 'country_name')->where("status", "active")->orderByRaw("country_name ASC")->get();
        return countriesdefaultResource::collection($data);
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
			'short_name' => 'required|max:10',
			'country_name' => 'required|max:150|unique:core_country'
		]);
		if ($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200); 
		$countries = new CoreCountry;
		$countries->short_name = $request->short_name;
		$countries->country_name = $request->country_name;
		$countries->status = 'active';
		$countries->inserted_by = $authUser->id;
		try{
			DB::beginTransaction();
			$countries->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new countriesListResource($countries)], "message" => "Record has been Successfully Inserted"], 201);
	}


	/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

	public function show(Request $request, $id){
		$countries = CoreCountry::findOrFail($id);
		$authUser = $request->user();
		return new countriesEditResource($countries);
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
		$countries = CoreCountry::findOrFail($id);
		$validator = Validator::make($request->all(), [
			'short_name' => 'required|max:10',
			'country_name' => 'required|max:150|unique:core_country,country_name,'.$id.',id'
		]);
		if($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200);
		$countries->short_name = $request->short_name;
		$countries->country_name = $request->country_name;
		$countries->status = $request->status;
		$countries->updated_by = $authUser->id;
		try {
			DB::beginTransaction();
			$countries->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new countriesListResource($countries)], "message" => "Record has been Successfully Updated"], 202);
	}


	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function destroy($id){
		$countries = CoreCountry::findOrFail($id);
		$countries->delete();
		return new countriesListResource($countries);
	}
}
