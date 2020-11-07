<?php

namespace App\Http\Controllers\Api\Veterinary\Masters;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Veterinary\Masters\VeterinaryTreatmentTypesM;
use App\Http\Resources\Veterinary\Masters\TreatmentTypes\treatmenttypesListResource as treatmenttypesListResource;
use App\Http\Resources\Veterinary\Masters\TreatmentTypes\treatmenttypesEditResource as treatmenttypesEditResource;
use App\Http\Resources\Veterinary\Masters\TreatmentTypes\treatmenttypesdefaultResource as treatmenttypesdefaultResource;
use Illuminate\Support\Facades\Validator;
use DB;

class TreatmentTypesController extends Controller
{
    


	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	public function index(Request $request) {
		$filterObj = $this->sortAndFilterOptions($request);
		$data = VeterinaryTreatmentTypesM::where($filterObj['filter'])->orderByRaw($filterObj['sort'])->paginate($filterObj['perPage']);
		return treatmenttypesListResource::collection($data);
	}


	/**
    *
    * Listing for select boxes
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function list(Request $request){
        $data = VeterinaryTreatmentTypesM::select('id', 'treatment_type_name')->orderByRaw("treatment_type_name ASC")->get();
        return treatmenttypesdefaultResource::collection($data);
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
			'treatment_type_name' => 'required|max:50|unique:veterinary_treatment_types_m',
			'description' => 'required|max:500'
		]);
		if ($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200); 
		$treatmentTypes = new VeterinaryTreatmentTypesM;
		$treatmentTypes->treatment_type_name = $request->treatment_type_name;
		$treatmentTypes->description = $request->description;
		$treatmentTypes->inserted_by = $authUser->id;
		try{
			DB::beginTransaction();
			$treatmentTypes->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new treatmenttypesListResource($treatmentTypes)], "message" => "Record has been Successfully Inserted"], 201);
	}


	/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

	public function show(Request $request, $id){
		$treatmentTypes = VeterinaryTreatmentTypesM::findOrFail($id);
		$authUser = $request->user();
		return new treatmenttypesEditResource($treatmentTypes);
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
		$treatmentTypes = VeterinaryTreatmentTypesM::findOrFail($id);
		$validator = Validator::make($request->all(), [
			'treatment_type_name' => 'required|max:50|unique:veterinary_treatment_types_m,treatment_type_name,'.$id.',id',
			'description' => 'required|max:500'
		]);
		if($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200);
		$treatmentTypes->treatment_type_name = $request->treatment_type_name;
		$treatmentTypes->description = $request->description;
		$treatmentTypes->updated_by = $authUser->id;
		try {
			DB::beginTransaction();
			$treatmentTypes->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new treatmenttypesListResource($treatmentTypes)], "message" => "Record has been Successfully Updated"], 202);
	}


	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function destroy($id){
		$treatmentTypes = VeterinaryTreatmentTypesM::findOrFail($id);
		$treatmentTypes->delete();
		return new treatmenttypesListResource($treatmentTypes);
	}
}
