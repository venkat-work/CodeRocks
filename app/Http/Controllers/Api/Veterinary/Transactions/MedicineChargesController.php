<?php

namespace App\Http\Controllers\Api\Veterinary\Transactions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Veterinary\Transactions\VeterinaryMedicineCharges;
use App\Http\Resources\Veterinary\Transactions\MedicineCharges\medicinechargesListResource as medicinechargesListResource;
use App\Http\Resources\Veterinary\Transactions\MedicineCharges\medicinechargesEditResource as medicinechargesEditResource;
use App\Http\Resources\Veterinary\Transactions\MedicineCharges\medicinechargesdefaultResource as medicinechargesdefaultResource;
use Illuminate\Support\Facades\Validator;
use DB;

class MedicineChargesController extends Controller
{



	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	public function index(Request $request) {
		$filterObj = $this->sortAndFilterOptions($request);
		$data = VeterinaryMedicineCharges::where($filterObj['filter'])->orderByRaw($filterObj['sort'])->paginate($filterObj['perPage']);
		return medicinechargesListResource::collection($data);
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
			'medicine_id' => 'required|numeric',
			'purchased_price' => 'required',
			'issued_price' => 'required',
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

		$overlapCheck = VeterinaryMedicineCharges::select('id', 'medicine_id')->where("medicine_id", "=", $request->medicine_id)->where("effective_to", ">=", $effective_from)->where("effective_from", "<=", $effective_from)->get();
		
		 if(count($overlapCheck)>0){
		 	return response()->json(["errors" => ["Entered date overlaps with already entered dates for selected medicine"], "message" => "Validation Failed"], 200);
		 }
	
		try{
			DB::beginTransaction();
			$medicineCharges = new VeterinaryMedicineCharges;
			$medicineDetails = VeterinaryMedicineCharges::select('id', 'medicine_id','effective_from')->where("medicine_id", $request->medicine_id)->whereNull("effective_to")->get();
			if(count($medicineDetails)>0){
				$medicineDetails[0]->effective_to = date( "Y-m-d", strtotime($effective_from . "-1 day"));
				if($medicineDetails[0]->effective_to < $medicineDetails[0]->effective_from){
					return response()->json(["errors" => ["Entered date should not be less than previous entered dates for selected medicine"], "message" => "Validation Failed"], 200); 
				}
				$medicineCharges->updated_by = $authUser->id;
				$medicineDetails[0]->save();
			} 
			
			$medicineCharges->medicine_id = $request->medicine_id;
			$medicineCharges->purchased_price = $request->purchased_price;
			$medicineCharges->issued_price = $request->issued_price;
			$medicineCharges->total_gst_percentage = $request->total_gst_percentage;
			$medicineCharges->total_gst_amount = $request->total_gst_amount;
			$medicineCharges->cgst_percentage = $request->cgst_percentage;
			$medicineCharges->cgst_amount = $request->cgst_amount;
			$medicineCharges->sgst_percentage = $request->sgst_percentage;
			$medicineCharges->sgst_amount = $request->sgst_amount;
			$medicineCharges->igst_percentage = $request->igst_percentage;
			$medicineCharges->igst_amount = $request->igst_amount;
			$medicineCharges->effective_from = $request->effective_from;
			$medicineCharges->inserted_by = $authUser->id;
			if(isset($request->effective_to))
				$medicineCharges->effective_to = $request->effective_to;
			$medicineCharges->inserted_by = $authUser->id;

			$medicineCharges->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new medicinechargesListResource($medicineCharges)], "message" => "Record has been Successfully Inserted"], 201);
	}


	/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

	public function show(Request $request, $id){
		$medicineCharges = VeterinaryMedicineCharges::findOrFail($id);
		$authUser = $request->user();
		return new medicinechargesEditResource($medicineCharges);
	}

	/**
    *
    * GET MEDICINES BY DATE
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function getMedicinesByDate($date){
    	$currentDate = $date;
        $data = VeterinaryMedicineCharges::select('id','medicine_id','issued_price','total_gst_percentage','total_gst_amount','cgst_percentage','cgst_amount','sgst_percentage','sgst_amount','igst_percentage','igst_amount')
        		->with(['medicine:id,material_name,uom_id','medicine.uom'])
        		->where("effective_from", "<=", $currentDate)
        		->where(function($query) use($currentDate) { $query->where("effective_to", ">=", $currentDate); $query->orWhereNull("effective_to"); })
        		->orderByRaw("id ASC")->get();
        return medicinechargesEditResource::collection($data);
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
		$medicineCharges = VeterinaryMedicineCharges::findOrFail($id);
		$validator = Validator::make($request->all(), [
			'medicine_id' => 'required|numeric',
			'purchased_price' => 'required',
			'issued_price' => 'required',
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
		if($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200);

		$effective_from = $request->effective_from;

		$overlapCheck = VeterinaryMedicineCharges::select('id', 'medicine_id')->where("medicine_id", "=", $request->medicine_id)->where("effective_to", ">=", $effective_from)->where("effective_from", "<=", $effective_from)->where("id", "!=", $id)->get();
		 if(count($overlapCheck)>0){
		 	return response()->json(["errors" => ["Entered date overlaps with already entered dates for selected medicine"], "message" => "Validation Failed"], 200);
		 }

		try {
			DB::beginTransaction();
			$medicineCharges = VeterinaryMedicineCharges::findOrFail($id);

			$medicineDetails = VeterinaryMedicineCharges::select('id', 'medicine_id','effective_from')->where("medicine_id", $request->medicine_id)->whereNull("effective_to")->where("id", "!=", $id)->get();
			if(count($medicineDetails)>0){
				$medicineDetails[0]->effective_to = date( "Y-m-d", strtotime($effective_from . "-1 day"));
				if($medicineDetails[0]->effective_to < $medicineDetails[0]->effective_from){
					return response()->json(["errors" => ["Entered date should not be less than previous entered dates for selected medicine"], "message" => "Validation Failed"], 200); 
				}
				$medicineCharges->updated_by = $authUser->id;
				$medicineDetails[0]->save();
			}

			
			$medicineCharges->medicine_id = $request->medicine_id;
			$medicineCharges->purchased_price = $request->purchased_price;
			$medicineCharges->issued_price = $request->issued_price;
			$medicineCharges->total_gst_percentage = $request->total_gst_percentage;
			$medicineCharges->total_gst_amount = $request->total_gst_amount;
			$medicineCharges->cgst_percentage = $request->cgst_percentage;
			$medicineCharges->cgst_amount = $request->cgst_amount;
			$medicineCharges->sgst_percentage = $request->sgst_percentage;
			$medicineCharges->sgst_amount = $request->sgst_amount;
			$medicineCharges->igst_percentage = $request->igst_percentage;
			$medicineCharges->igst_amount = $request->igst_amount;
			$medicineCharges->effective_from = $request->effective_from;
			$medicineCharges->updated_by = $authUser->id;
			if(isset($request->effective_to))
				$medicineCharges->effective_to = $request->effective_to;
			
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new medicinechargesListResource($medicineCharges)], "message" => "Record has been Successfully Updated"], 202);
	}


	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function destroy($id){
		$medicineCharges = VeterinaryMedicineCharges::findOrFail($id);
		$medicineCharges->delete();
		return new medicinechargesListResource($medicineCharges);
	}
}
