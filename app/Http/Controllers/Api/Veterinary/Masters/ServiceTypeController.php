<?php

namespace App\Http\Controllers\Api\Veterinary\Masters;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Veterinary\Masters\VeterinaryServiceTypeM;
use App\Http\Resources\Veterinary\Masters\ServiceType\servicetypeListResource as servicetypeListResource;
use App\Http\Resources\Veterinary\Masters\ServiceType\servicetypeEditResource as servicetypeEditResource;
use App\Http\Resources\Veterinary\Masters\ServiceType\servicetypedefaultResource as servicetypedefaultResource;
use Illuminate\Support\Facades\Validator;
use DB;

class ServiceTypeController extends Controller
{
    


	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	public function index(Request $request) {
		$filterObj = $this->sortAndFilterOptions($request);
		$data = VeterinaryServiceTypeM::where($filterObj['filter'])->orderByRaw($filterObj['sort'])->paginate($filterObj['perPage']);
		return servicetypeListResource::collection($data);
	}

	/**
    *
    * Listing for select boxes
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function list(Request $request){
        $data = VeterinaryServiceTypeM::select('id', 'service_name')->orderByRaw("service_name ASC")->get();
        return servicetypedefaultResource::collection($data);
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
			'service_name' => 'required|max:50|unique:veterinary_service_type_m',
			'description' => 'required|max:500'
		]);
		if ($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200); 
		$serviceType = new VeterinaryServiceTypeM;
		$serviceType->service_name = $request->service_name;
		$serviceType->description = $request->description;
		$serviceType->inserted_by = $authUser->id;
		try{
			DB::beginTransaction();
			$serviceType->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new servicetypeListResource($serviceType)], "message" => "Record has been Successfully Inserted"], 201);
	}


	/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

	public function show(Request $request, $id){
		$serviceType = VeterinaryServiceTypeM::findOrFail($id);
		$authUser = $request->user();
		return new servicetypeEditResource($serviceType);
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
		$serviceType = VeterinaryServiceTypeM::findOrFail($id);
		$validator = Validator::make($request->all(), [
			'service_name' => 'required|max:50|unique:veterinary_service_type_m,service_name,'.$id.',id',
			'description' => 'required|max:500'
		]);
		if($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200);
		$serviceType->service_name = $request->service_name;
		$serviceType->description = $request->description;
		$serviceType->updated_by = $authUser->id;
		try {
			DB::beginTransaction();
			$serviceType->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new servicetypeListResource($serviceType)], "message" => "Record has been Successfully Updated"], 202);
	}


	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function destroy($id){
		$serviceType = VeterinaryServiceTypeM::findOrFail($id);
		$serviceType->delete();
		return new servicetypeListResource($serviceType);
	}
}
