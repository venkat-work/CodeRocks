<?php

namespace App\Http\Controllers\Api\Veterinary\Transactions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Veterinary\Transactions\VeterinaryDeadHorseDetails;
use App\Models\Racing\Masters\RacingHorseM;
use App\Http\Resources\Veterinary\Transactions\VeterinaryDeadHorseEntry\veterinarydeadhorseentryListResource as veterinarydeadhorseentryListResource;
use App\Http\Resources\Veterinary\Transactions\VeterinaryDeadHorseEntry\veterinarydeadhorseentryEditResource as veterinarydeadhorseentryEditResource;
use App\Http\Resources\Veterinary\Transactions\VeterinaryDeadHorseEntry\veterinarydeadhorseentrydefaultResource as veterinarydeadhorseentrydefaultResource;
use Illuminate\Support\Facades\Validator;
use DB;

class VeterinaryDeadHorseEntryController extends Controller
{



	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

	public function index(Request $request) {
		$filterObj = $this->sortAndFilterOptions($request, 'veterinary_dead_horse_details');
		$data = VeterinaryDeadHorseDetails::
				select("veterinary_dead_horse_details.*",'horse.horse_name','trainer.party_publicationname')
				->leftJoin('racing_party_registrations as trainer', 'veterinary_dead_horse_details.trainer_id', '=', 'trainer.id')
				->leftJoin('racing_horse_m as horse', 'veterinary_dead_horse_details.horse_id', '=', 'horse.id')
				->where($filterObj['filter'])->orderByRaw($filterObj['sort'])->paginate($filterObj['perPage']);

		return veterinarydeadhorseentryListResource::collection($data);
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
			'horse_id' => 'required|numeric',
			'death_date' => 'required',
			'trainer_id' => 'required',
			'death_place' => 'required|max:500',
			'remarks' => 'required',
			'disposal_method' => 'required'
		]);
		if ($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200);
		$veterinaryDeadHorseEntry = new VeterinaryDeadHorseDetails;
		$veterinaryDeadHorseEntry->horse_id = $request->horse_id;
		$veterinaryDeadHorseEntry->death_date = $request->death_date;
		$veterinaryDeadHorseEntry->trainer_id = $request->trainer_id;
		$veterinaryDeadHorseEntry->death_place = $request->death_place;
		$veterinaryDeadHorseEntry->remarks = $request->remarks;
		$veterinaryDeadHorseEntry->disposal_method = $request->disposal_method;
		$veterinaryDeadHorseEntry->inserted_by = $authUser->id;
		try{
			DB::beginTransaction();
			$veterinaryDeadHorseEntry->save();
			
			$horse = RacingHorseM::find($request->horse_id);
			$horse->is_dead = "Y";
			$horse->gatepass_type = "P";
			$horse->save();

			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new veterinarydeadhorseentryListResource($veterinaryDeadHorseEntry)], "message" => "Record has been Successfully Inserted"], 201);
	}


	/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

	public function show(Request $request, $id){
		$veterinaryDeadHorseEntry = VeterinaryDeadHorseDetails::findOrFail($id);
		$authUser = $request->user();
		return new veterinarydeadhorseentryEditResource($veterinaryDeadHorseEntry);
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

		$veterinaryDeadHorseEntry = VeterinaryDeadHorseDetails::findOrFail($id);
		$validator = Validator::make($request->all(), [
			'horse_id' => 'required|numeric',
			'death_date' => 'required',
			'trainer_id' => 'required',
			'death_place' => 'required|max:500',
			'remarks' => 'required',
			'disposal_method' => 'required'
		]);
		if($validator->fails()) return response()->json(["errors" => $validator->messages(), "message" => "Validation Failed"], 200);
		$veterinaryDeadHorseEntry->horse_id = $request->horse_id;
		$veterinaryDeadHorseEntry->death_date = $request->death_date;
		$veterinaryDeadHorseEntry->death_place = $request->death_place;
		$veterinaryDeadHorseEntry->remarks = $request->remarks;
		$veterinaryDeadHorseEntry->trainer_id = $request->trainer_id;
		$veterinaryDeadHorseEntry->disposal_method = $request->disposal_method;
		$veterinaryDeadHorseEntry->updated_by = $authUser->id;
		//print_r($veterinaryDeadHorseEntry->toJson());
		try {
			DB::beginTransaction();
			$veterinaryDeadHorseEntry->save();
			DB::commit();
		} catch(Exception $e){
			DB::rollback();
		}
		return response()->json(["success" => ["data" => new veterinarydeadhorseentryListResource($veterinaryDeadHorseEntry)], "message" => "Record has been Successfully Updated"], 202);
	}


	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function destroy($id){
		$veterinaryDeadHorseEntry = VeterinaryDeadHorseDetails::findOrFail($id);
		$veterinaryDeadHorseEntry->delete();
		return new veterinarydeadhorseentryListResource($veterinaryDeadHorseEntry);
	}
}
