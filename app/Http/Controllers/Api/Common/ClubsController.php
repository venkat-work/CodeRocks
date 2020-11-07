<?php

namespace App\Http\Controllers\Api\Common;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Common\CoreClubs;
use App\Http\Resources\Common\Clubs\clubsListResource as clubsListResource;
use App\Http\Resources\Common\Clubs\clubsEditResource as clubsEditResource;
use App\Http\Resources\Common\Clubs\clubsdefaultResource as clubsdefaultResource;
use Illuminate\Support\Facades\Validator;
use DB;

class ClubsController extends Controller
{
    


	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	public function index(Request $request) {
		$filterObj = $this->sortAndFilterOptions($request);
		$data = CoreClubs::where($filterObj['filter'])->orderByRaw($filterObj['sort'])->paginate($filterObj['perPage']);
		return clubsListResource::collection($data);
	}

	/**
    *
    * Listing for select boxes
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function list(Request $request){
        $data = CoreClubs::select('id', 'short_name', 'club_name')->where("status", "active")->orderByRaw("club_name ASC")->get();
        return clubsdefaultResource::collection($data);
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
			'short_name' => 'required|max:10|unique:core_clubs',
			'club_name' => 'required|max:150',
			'address' => 'required|max:250'
		]);
		if ($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200); 
		$clubs = new CoreClubs;
		$clubs->short_name = $request->short_name;
		$clubs->club_name = $request->club_name;
		$clubs->address = $request->address;
		$clubs->status = 'active';
		$clubs->inserted_by = $authUser->id;
		try{
			DB::beginTransaction();
			$clubs->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new clubsListResource($clubs)], "message" => "Record has been Successfully Inserted"], 201);
	}


	/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

	public function show(Request $request, $id){
		$clubs = CoreClubs::findOrFail($id);
		$authUser = $request->user();
		return new clubsEditResource($clubs);
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
		$clubs = CoreClubs::findOrFail($id);
		$validator = Validator::make($request->all(), [
			'short_name' => 'required|max:10|unique:core_clubs,short_name,'.$id.',id',
			'club_name' => 'required|max:150',
			'address' => 'required|max:250'
		]);
		if($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200);
		$clubs->short_name = $request->short_name;
		$clubs->club_name = $request->club_name;
		$clubs->address = $request->address;
		$clubs->status = $request->status;
		$clubs->updated_by = $authUser->id;
		try {
			DB::beginTransaction();
			$clubs->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new clubsListResource($clubs)], "message" => "Record has been Successfully Updated"], 202);
	}


	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function destroy($id){
		$clubs = CoreClubs::findOrFail($id);
		$clubs->delete();
		return new clubsListResource($clubs);
	}
}
