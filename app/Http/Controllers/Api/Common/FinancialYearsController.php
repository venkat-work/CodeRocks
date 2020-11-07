<?php

namespace App\Http\Controllers\Api\Common;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Common\CoreFinancialYearM;
use App\Http\Resources\Common\FinancialYears\financialyearsListResource as financialyearsListResource;
use App\Http\Resources\Common\FinancialYears\financialyearsEditResource as financialyearsEditResource;
use App\Http\Resources\Common\FinancialYears\financialyearsdefaultResource as financialyearsdefaultResource;
use Illuminate\Support\Facades\Validator;
use DB;

class FinancialYearsController extends Controller
{
    


	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	public function index(Request $request) {
		$filterObj = $this->sortAndFilterOptions($request);
		$data = CoreFinancialYearM::where($filterObj['filter'])->orderByRaw($filterObj['sort'])->paginate($filterObj['perPage']);
		return financialyearsListResource::collection($data);
	}


	/**
    *
    * Listing for select boxes
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function list(Request $request){
        $data = CoreFinancialYearM::select('id', 'financial_year','is_current')->orderByRaw("financial_year ASC")->get();
        return financialyearsdefaultResource::collection($data);
    }


/**
    *
    * Listing for select boxes
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function monthslist(Request $request){
        $data = array(array("id"=>"4", "month"=>"April"), array("id"=>"5", "month"=>"May"), array("id"=>"6", "month"=>"June"), array("id"=>"7","month"=>"July"), array("id"=>"8", "month"=>"August"), array("id"=>"9", "month"=>"September"), array("id" =>"10", "month"=>"October"), array("id"=>"11", "month"=>"November"), array("id"=>"12", "month" => "Decemeber"), array("id" => "1", "month"=>"January"), array("id"=>"2", "month"=>"February"), array("id"=>"3", "month"=>"March"));
        return array("data" => $data);//financialyearsdefaultResource::collection($data);
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
			'financial_year' => 'required|max:50|unique:core_financial_year_m',
			'is_current' => 'required|max:2'
		]);
		if ($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200); 
		$financialYears = new CoreFinancialYearM;
		$financialYears->financial_year = $request->financial_year;
		$financialYears->is_current = $request->is_current;
		$financialYears->inserted_by = $authUser->id;
		try{
			DB::beginTransaction();
			$financialYears->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new financialyearsListResource($financialYears)], "message" => "Record has been Successfully Inserted"], 201);
	}


	/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

	public function show(Request $request, $id){
		$financialYears = CoreFinancialYearM::findOrFail($id);
		$authUser = $request->user();
		return new financialyearsEditResource($financialYears);
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
		$financialYears = CoreFinancialYearM::findOrFail($id);
		$validator = Validator::make($request->all(), [
			'financial_year' => 'required|max:50|unique:core_financial_year_m,financial_year,'.$id.',id',
			'is_current' => 'required|max:2'
		]);
		if($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200);
		$financialYears->financial_year = $request->financial_year;
		$financialYears->is_current = $request->is_current;
		$financialYears->updated_by = $authUser->id;
		try {
			DB::beginTransaction();
			$financialYears->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new financialyearsListResource($financialYears)], "message" => "Record has been Successfully Updated"], 202);
	}


	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function destroy($id){
		$financialYears = CoreFinancialYearM::findOrFail($id);
		$financialYears->delete();
		return new financialyearsListResource($financialYears);
	}
}
