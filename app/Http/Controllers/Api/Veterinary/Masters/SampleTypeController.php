<?php

namespace App\Http\Controllers\Api\Veterinary\Masters;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Veterinary\Masters\VeterinarySampleTypeM;
use App\Http\Resources\Veterinary\Masters\SampleType\sampletypeListResource as sampletypeListResource;
use App\Http\Resources\Veterinary\Masters\SampleType\sampletypeEditResource as sampletypeEditResource;
use App\Http\Resources\Veterinary\Masters\SampleType\sampletypedefaultResource as sampletypedefaultResource;
use Illuminate\Support\Facades\Validator;
use DB;

class SampleTypeController extends Controller
{
    


	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	public function index(Request $request) {
		$filterObj = $this->sortAndFilterOptions($request);
		$data = VeterinarySampleTypeM::where($filterObj['filter'])->orderByRaw($filterObj['sort'])->paginate($filterObj['perPage']);
		return sampletypeListResource::collection($data);
	}

	/**
    *
    * Listing for select boxes
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function list(Request $request){
        $data = VeterinarySampleTypeM::select('id', 'sample_name')->orderByRaw("sample_name ASC")->get();
        return sampletypedefaultResource::collection($data);
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
			'sample_name' => 'required|max:50|unique:veterinary_sample_type_m',
			'description' => 'required|max:500'
		]);
		if ($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200); 
		$sampleType = new VeterinarySampleTypeM;
		$sampleType->sample_name = $request->sample_name;
		$sampleType->description = $request->description;
		$sampleType->inserted_by = $authUser->id;
		try{
			DB::beginTransaction();
			$sampleType->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new sampletypeListResource($sampleType)], "message" => "Record has been Successfully Inserted"], 201);
	}


	/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

	public function show(Request $request, $id){
		$sampleType = VeterinarySampleTypeM::findOrFail($id);
		$authUser = $request->user();
		return new sampletypeEditResource($sampleType);
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
		$sampleType = VeterinarySampleTypeM::findOrFail($id);
		$validator = Validator::make($request->all(), [
			'sample_name' => 'required|max:50|unique:veterinary_sample_type_m,sample_name,'.$id.',id',
			'description' => 'required|max:500'
		]);
		if($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200);
		$sampleType->sample_name = $request->sample_name;
		$sampleType->description = $request->description;
		$sampleType->updated_by = $authUser->id;
		try {
			DB::beginTransaction();
			$sampleType->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new sampletypeListResource($sampleType)], "message" => "Record has been Successfully Updated"], 202);
	}


	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function destroy($id){
		$sampleType = VeterinarySampleTypeM::findOrFail($id);
		$sampleType->delete();
		return new sampletypeListResource($sampleType);
	}
}
