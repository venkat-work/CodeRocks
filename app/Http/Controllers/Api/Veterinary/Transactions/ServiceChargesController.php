<?php

namespace App\Http\Controllers\Api\Veterinary\Transactions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Veterinary\Transactions\VeterinaryServiceCharges;
use App\Http\Resources\Veterinary\Transactions\ServiceCharges\servicechargesListResource as servicechargesListResource;
use App\Http\Resources\Veterinary\Transactions\ServiceCharges\servicechargesEditResource as servicechargesEditResource;
use App\Http\Resources\Veterinary\Transactions\ServiceCharges\servicechargesdefaultResource as servicechargesdefaultResource;
use Illuminate\Support\Facades\Validator;
use DB;

class ServiceChargesController extends Controller
{



	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	public function index(Request $request) {
		$filterObj = $this->sortAndFilterOptions($request,'veterinary_service_charges');
		$data = VeterinaryServiceCharges::
				select("veterinary_service_charges.*", 'service.service_name')
				->leftJoin('veterinary_service_type_m as service', 'veterinary_service_charges.service_id', '=', 'service.id')
				->where($filterObj['filter'])->orderByRaw($filterObj['sort'])->paginate($filterObj['perPage']);
		return servicechargesListResource::collection($data);
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
			'service_id' => 'required|numeric',
			'service_actual_amount' => 'required',
			'service_amount' => 'required',
			'total_gst_percentage' => 'required',
			'total_gst_amount' => 'required',
			'cgst_percentage' => 'required',
			'cgst_amount' => 'required',
			'sgst_percentage' => 'required',
			'sgst_amount' => 'required',
			'igst_percentage' => 'required',
			'igst_amount' => 'required',
			'effective_from' => 'required'
		]);
		if ($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200);

		$effective_from = $request->effective_from;

		$overlapCheck = VeterinaryServiceCharges::select('id', 'service_id')->where("service_id", $request->service_id)->where("effective_to", ">=", $effective_from)->where("effective_from", "<=", $effective_from)->get();
		 if(count($overlapCheck)>0){
		 	return response()->json(["errors" => ["Entered date overlaps with already entered dates for selected service"], "message" => "Validation Failed"], 200);
		 }

		
		
		try{
			DB::beginTransaction();

			$serviceCharges = new VeterinaryServiceCharges;
			
			$serviceDetails = VeterinaryServiceCharges::select('id', 'service_id','effective_from')->where("service_id", $request->service_id)->whereNull("effective_to")->get();
			if(count($serviceDetails)>0){
				$serviceDetails[0]->effective_to = date( "Y-m-d", strtotime($effective_from . "-1 day"));
				if($serviceDetails[0]->effective_to < $serviceDetails[0]->effective_from){
					return response()->json(["errors" => ["Entered date should not be less than previous entered dates for selected service"], "message" => "Validation Failed"], 200); 
				}
				$serviceCharges->updated_by = $authUser->id;
				$serviceDetails[0]->save();
			} 

			
			$serviceCharges->service_id = $request->service_id;
			$serviceCharges->service_actual_amount = $request->service_actual_amount;
			$serviceCharges->service_amount = $request->service_amount;
			$serviceCharges->total_gst_percentage = $request->total_gst_percentage;
			$serviceCharges->total_gst_amount = $request->total_gst_amount;
			$serviceCharges->cgst_percentage = $request->cgst_percentage;
			$serviceCharges->cgst_amount = $request->cgst_amount;
			$serviceCharges->sgst_percentage = $request->sgst_percentage;
			$serviceCharges->sgst_amount = $request->sgst_amount;
			$serviceCharges->igst_percentage = $request->igst_percentage;
			$serviceCharges->igst_amount = $request->igst_amount;
			$serviceCharges->effective_from = $request->effective_from;
			$serviceCharges->inserted_by = $authUser->id;
			if(isset($request->effective_to))
				$serviceCharges->effective_to = $request->effective_to;

			$serviceCharges->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new servicechargesListResource($serviceCharges)], "message" => "Record has been Successfully Inserted"], 201);
	}


	/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

	public function show(Request $request, $id){
		$serviceCharges = VeterinaryServiceCharges::findOrFail($id);
		$authUser = $request->user();
		return new servicechargesEditResource($serviceCharges);
	}

	/**
    *
    * GET Services BY DATE
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function getServicesByDate($date){
    	$currentDate = $date;
        $data = VeterinaryServiceCharges::select('id','service_id','service_actual_amount','service_amount','total_gst_percentage','total_gst_amount','cgst_percentage','cgst_amount','sgst_percentage','sgst_amount','igst_percentage','igst_amount')
        		->with(['service:id,service_name'])
        		->where("effective_from", "<=", $currentDate)
        		->where(function($query) use($currentDate) { $query->where("effective_to", ">=", $currentDate); $query->orWhereNull("effective_to"); })
        		->orderByRaw("id ASC")->get();
        return servicechargesEditResource::collection($data);
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
		$serviceCharges = VeterinaryServiceCharges::findOrFail($id);
		$validator = Validator::make($request->all(), [
			'service_id' => 'required|numeric',
			'service_actual_amount' => 'required',
			'service_amount' => 'required',
			'total_gst_percentage' => 'required',
			'cgst_percentage' => 'required',
			'cgst_amount' => 'required',
			'sgst_percentage' => 'required',
			'sgst_amount' => 'required',
			'igst_percentage' => 'required',
			'igst_amount' => 'required',
			'effective_from' => 'required'
		]);
		if($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200);
		
		$effective_from = $request->effective_from;

		$overlapCheck = VeterinaryServiceCharges::select('id', 'service_id')->where("service_id", "=", $request->service_id)->where("effective_to", ">=", $effective_from)->where("effective_from", "<=", $effective_from)->where("id", "!=", $id)->get();
		 if(count($overlapCheck)>0){
		 	return response()->json(["errors" => ["Entered date overlaps with already entered dates for selected service"], "message" => "Validation Failed"], 200);
		 }

		try {
			DB::beginTransaction();
			$serviceCharges = VeterinaryServiceCharges::findOrFail($id);
			
			$serviceDetails = VeterinaryServiceCharges::select('id', 'service_id','effective_from')->where("service_id", $request->service_id)->whereNull("effective_to")->where("id", "!=", $id)->get();
			if(count($serviceDetails)>0){
				$serviceDetails[0]->effective_to = date( "Y-m-d", strtotime($effective_from . "-1 day"));
				if($serviceDetails[0]->effective_to < $serviceDetails[0]->effective_from){
					return response()->json(["errors" => ["Entered date should not be less than previous entered dates for selected service"], "message" => "Validation Failed"], 200); 
				}
				$serviceCharges->updated_by = $authUser->id;
				$serviceDetails[0]->save();
			} 

			$serviceCharges->service_id = $request->service_id;
			$serviceCharges->service_actual_amount = $request->service_actual_amount;
			$serviceCharges->service_amount = $request->service_amount;
			$serviceCharges->total_gst_percentage = $request->total_gst_percentage;
			$serviceCharges->total_gst_amount = $request->total_gst_amount;
			$serviceCharges->cgst_percentage = $request->cgst_percentage;
			$serviceCharges->cgst_amount = $request->cgst_amount;
			$serviceCharges->sgst_percentage = $request->sgst_percentage;
			$serviceCharges->sgst_amount = $request->sgst_amount;
			$serviceCharges->igst_percentage = $request->igst_percentage;
			$serviceCharges->igst_amount = $request->igst_amount;
			$serviceCharges->effective_from = $request->effective_from;
			$serviceCharges->updated_by = $authUser->id;
			if(isset($request->effective_to))
				$serviceCharges->effective_to = $request->effective_to;
				$serviceCharges->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new servicechargesListResource($serviceCharges)], "message" => "Record has been Successfully Updated"], 202);
	}


	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function destroy($id){
		$serviceCharges = VeterinaryServiceCharges::findOrFail($id);
		$serviceCharges->delete();
		return new servicechargesListResource($serviceCharges);
	}
}
