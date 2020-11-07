<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Department;
use App\Models\Workflow\Workflow;
use App\Models\Workflow\WorkflowDetails;
use Uuid;
use DB;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function sortAndFilterOptions($request, $defaultTable="", $order="id desc", $where=""){
        $response = array();
        $perPage = $request->query('perPage');
        if($perPage == ""){
            $perPage = 15;
        }

        

        $sort = $request->query('sort');
        
        if($sort != ""){
            $sortArray = explode(",", $sort);
            $order = "";
            foreach($sortArray as $key=>$val){
                $orderType = " ASC, ";
                $column = trim($val);

                $temp = substr($column, 0, 1);
                if($temp == "-"){
                    $orderType = " DESC, ";
                    $column = substr($column, 1);
                } elseif($temp == "+"){
                    $column = substr($column, 1);
                }

                //finding sub table or not
                $temp = explode(".", $column);
                if(count($temp) == 1)
                    if($defaultTable != "")
                        $column = $defaultTable.".".$column;
                else {
                    if(strtolower($temp[0]) == 'custom'){
                        $column = $temp[1];
                    }
                }

                $order .= $column.$orderType;
            }
            $order = trim($order, ", ");
        } else {
            if($defaultTable != "")
                $order = $defaultTable.".".$order;
        }

        $filter = $request->query("filter");
        if($where == ""){
            $column = "id";
            if($defaultTable != "")
                $column = $defaultTable.".".$column;
            $where = array(array($column, "!=", "-1"));
        }

        //filter={"distance:eq":"11","status":"active"}
        if($filter != ""){
            $searchArray = json_decode($filter, true);
            $where = array();
            foreach($searchArray as $key => $val){
                $column = "";
                $operator = "LIKE";
                $value = $val;

                $temp = explode(":", $key);
                if(count($temp) == 2){
                    if(strtolower($temp[1]) == 'eq')
                        $operator = "=";
                }
                $column = $temp[0];

                if($operator == 'LIKE')
                    $value = "%".$value."%";

                //$findColumnGroup = explode(".", $column);

                //if(count($findColumnGroup) == 1)
                    //finding sub table or not
                    $temp = explode(".", $column);
                    if(count($temp) == 1)
                        if($defaultTable != "")
                            $column = $defaultTable.".".$column;
                    else {
                        if(strtolower($temp[0]) == 'custom'){
                            $column = $temp[1];
                        }
                    }

                    $where[] = array($column, $operator, $value);
                //else{
                //    $grpName = $findColumnGroup[0];
                //    if(!isset($response[$grpName]))
                //        $response[$grpName] = array();
                //    $response[$grpName][] = array($findColumnGroup[1], $operator, $value);
                //}
            }
        }
        $response['sort'] = $order;
        $response['filter'] = $where;
        $response['perPage'] = $perPage;

        return $response;
    }

    function workflowInitiated($record, $workflowJson, $user){

        $user->departmentId = 1;
        $user->roleId = 2;

        $uuid = Uuid::generate()->string;
    	DB::table($workflowJson['table_name'])->where("id", $record->id)->update(['workflow_id' => $uuid]);

    	$order = 1;
    	$workflow = new Workflow;
    	$workflow->id 				= $uuid;
    	$workflow->module_name 		= $workflowJson['module_name'];
    	$workflow->history 			= json_encode(array("1" => $record));

        $workflowJsonData = str_replace("#current#", $user->departmentId, json_encode($workflowJson));
    	$workflow->workflow_json 	= $workflowJsonData;
    	$workflow->current_status_index = $order;
    	$workflow->department_id 	= $user->departmentId;
    	$workflow->created_at 		= date("Y-m-d H:i:s");
    	$workflow->created_by		= $user->id;
    	$workflow->is_closed 		= 0;
    	$workflow->save();

    	$workflowDetails = new WorkflowDetails;
    	$workflowDetails->workflow_id 	= $uuid;

    	$workflowDetails->workflow_status_index 		= $order;
    	$workflowDetails->department_id = $user->departmentId;
    	$workflowDetails->role_id 		= $user->roleId;
    	$workflowDetails->user_id		= $user->id;
    	$workflowDetails->current_status= $record->status;

    	$workflowDetails->notification  = $workflowJson['notification'];
    	$workflowDetails->remarks 		= "";
    	$workflowDetails->created_at 	= date("Y-m-d H:i:s");
    	$workflowDetails->save();

    	return ["status" => true, "workflow_id" => $uuid];
    	/*$departments = array();
    	$roles = array();
    	foreach($workflowJson['transitions'] as $status => $transaction){
    		foreach($transaction['roles'] as $id => $action){
    			if(!isset($roles[$action])){
    				$roles[$action] = array();
    			}
    			$roles[$action][] = $id;
    		}

    		$departments[$status] = array();
    		foreach($transaction['departments'] as $id => $action){
    			$departments[$status][] = $action;
    		}
    	}

    	foreach($workflowJson['actions'] as $status => $actions){
    		foreach($actions as $action){
		    	$workflowDetails = new WorkflowDetails;
		    	$workflowDetails->workflow_id 	= $data->workflow_id;
		    	$workflowDetails->label_display = $action['label'];
		    	$workflowDetails->value_display = $action['value'];

		    	$workflowDetails->order_id 		= $order;
		    	$workflowDetails->department_id = "";
		    	$workflowDetails->role_id 		= "";
		    	$workflowDetails->user_id		= "";

		    	$workflowDetails->remarks 		= "";
		    	$workflowDetails->created_at 	= date("Y-m-d H:i:s");
		    	$workflowDetails->save();
	    	}
	    	$order ++;
    	}*/

    }

    function workflowOptions($data, $user){

        $departmentId = 1;
        $roleId = 10;
    	if(!isset($data->workflow_id)){
    		return ["status" => false, "message" => "Workflow_id column not available or empty"];
    	}

    	//Fetching the workflow object based on workflow id
    	$workflow = Workflow::findOrFail($data->workflow_id);
    	if($workflow->is_closed == 1){
    		return ["status" => false, "message" => "Workflow is closed"];
    	} else {
    		$status = $data->status;

    		//Fetching the workflow json object and converting to json
    		$workflowJson = str_replace("#current#",$departmentId, $workflow->workflow_json);
    		$workflowJson = json_decode($workflowJson, true);

    		//checking the current status exists workflow json or not
    		if(isset($workflowJson['transitions'][$status])){
    			$transaction = $workflowJson['transitions'][$status];

    			//Checking the department, relates to this workflow or not
    			if(in_array($departmentId, $transaction['departments'])){
    				//Checking the roles, relates to this workflow or not
    				$action = "";
    				foreach($transaction['roles'] as $role => $trans){
    					if($role == $roleId){
    						$action = $trans;
    					}
    				}
    				if($action == ""){
    					return ["status" => false, "message" => "Workflow_id is not related to this role"];
    				} else {
    					$actions = $workflowJson['actions'][$action];
    					$obj = [];
    					foreach($actions as $action){
    						$obj[] = ["label" => $action['label'], "value" => $action['value']];
    					}
                        $editFields = array();
                        if(isset($workflowJson['edit_fields'][$status]))
                            $editFields = $workflowJson['edit_fields'][$status];

    					return ["status" => true, "data" => ["actions" =>$obj, "edit_fields" => $editFields]];
    				}
    			} else {
    				return ["status" => false, "message" => "Workflow is not related to this department"];
    			}
    		} else {
    			return ["status" => false, "message" => "Status is not defined in workflow"];
    		}
    	}
    }

    function workflowUpdated($record, $user){

        $user->departmentId = 1;
        $user->roleId = 2;

        if(!isset($record->workflow_id)){
            return ["status" => false, "message" => "Workflow_id column not available or empty"];
        }

        //Fetching the workflow object based on workflow id
        $workflow = Workflow::findOrFail($record->workflow_id);
        if($workflow->is_closed == 1){
            return ["status" => false, "message" => "Workflow is closed"];
        } else {

            $workflowJson = json_decode($workflow['workflow_json'], true);

            //updating the workflow table
            $workflow->current_status_index +=1;
            $history = json_decode($workflow->history, true);
            $history[count($history) + 1] = $record;
            $workflow->history = json_encode($history);
            $workflow->save();

            //inserting workflow details table
            $workflowDetails = new WorkflowDetails;
            $workflowDetails->workflow_id   = $record->workflow_id;

            $workflowDetails->workflow_status_index = $workflow->current_status_index + 1;
            $workflowDetails->department_id = $user->departmentId;
            $workflowDetails->role_id       = $user->roleId;
            $workflowDetails->user_id       = $user->id;

            $workflowDetails->current_status= 'updated';
            $workflowDetails->notification  = $workflowJson['notification'];
            $workflowDetails->remarks       = "";
            $workflowDetails->created_at    = date("Y-m-d H:i:s");
            $workflowDetails->save();
        }
    }

}
