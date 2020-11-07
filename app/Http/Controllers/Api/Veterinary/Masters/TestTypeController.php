<?php

namespace App\Http\Controllers\Api\Veterinary\Masters;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Veterinary\Masters\VeterinaryTestTypeM;
use App\Http\Resources\Veterinary\Masters\TestType\testtypeListResource as testtypeListResource;
use App\Http\Resources\Veterinary\Masters\TestType\testtypeEditResource as testtypeEditResource;
use App\Http\Resources\Veterinary\Masters\TestType\testtypedefaultResource as testtypedefaultResource;
use Illuminate\Support\Facades\Validator;
use DB;

class TestTypeController extends Controller
{
    


	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	public function index(Request $request) {
		$filterObj = $this->sortAndFilterOptions($request);
		$data = VeterinaryTestTypeM::where($filterObj['filter'])->orderByRaw($filterObj['sort'])->paginate($filterObj['perPage']);
		return testtypeListResource::collection($data);
	}


	/**
    *
    * Listing for select boxes
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function list(Request $request){
        $data = VeterinaryTestTypeM::select('id', 'test_type')->orderByRaw("test_type ASC")->get();
        return testtypedefaultResource::collection($data);
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
			'test_category' => 'required|max:20',
			'test_type' => 'required|max:50|unique:veterinary_test_type_m',
			'description' => 'required|max:500'
		]);
		if ($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200); 
		$testType = new VeterinaryTestTypeM;
		$testType->test_category = $request->test_category;
		$testType->test_type = $request->test_type;
		$testType->description = $request->description;
		$testType->inserted_by = $authUser->id;
		try{
			DB::beginTransaction();
			$testType->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new testtypeListResource($testType)], "message" => "Record has been Successfully Inserted"], 201);
	}


	/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

	public function show(Request $request, $id){
		$testType = VeterinaryTestTypeM::findOrFail($id);
		$authUser = $request->user();
		return new testtypeEditResource($testType);
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
		$testType = VeterinaryTestTypeM::findOrFail($id);
		$validator = Validator::make($request->all(), [
			'test_category' => 'required|max:20',
			'test_type' => 'required|max:50|unique:veterinary_test_type_m,test_type,'.$id.',id',
			'description' => 'required|max:500'
		]);
		if($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200);
		$testType->test_category = $request->test_category;
		$testType->test_type = $request->test_type;
		$testType->description = $request->description;
		$testType->updated_by = $authUser->id;
		try {
			DB::beginTransaction();
			$testType->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new testtypeListResource($testType)], "message" => "Record has been Successfully Updated"], 202);
	}


	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function destroy($id){
		$testType = VeterinaryTestTypeM::findOrFail($id);
		$testType->delete();
		return new testtypeListResource($testType);
	}
}
