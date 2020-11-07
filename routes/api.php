<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
 });*/
//Route::get('/', 'Api\AuthController@login');

//Route::get('api', 'Api\AuthController@login');


Route::prefix('v1')->group(function(){
	Route::post('login', 'Api\AuthController@login');
	Route::post('register', 'Api\AuthController@register');

	Route::group(['middleware' => 'auth:api'], function(){
		Route::get('user', 'Api\AuthController@user');
		Route::get('me', 'Api\AuthController@getUser');

		//Route::post('getUser', 'Api\AuthController@getUser');
		Route::apiResource('workflows', 'API\WorkflowController');
		Route::apiResource('departments', 'API\DepartmentsController');

		Route::get('roles/list','Api\RolesController@rolesList');
		Route::get('roles/permissions/{id}','Api\RolesController@rolespermissions');
		Route::resource('roles','Api\RolesController');
		
		Route::get('users/list','Api\UsersController@usersList');
		Route::get('users/permissions','Api\UsersController@userPermissions');

		Route::resource('users','Api\UsersController');
		Route::resource('assignrole','Api\AssignRoleController')->except(['create','edit']);

		//Racing related URL's
 
 
			Route::resource('racinghorseequipmentlinking', 'Api\Racing\Transactions\RacingHorseEquipmentLinkingController');

		});

		Route::prefix('racingaccounts')->group(function(){
			Route::resource('stakestaxesm', 'Api\RacingAccounts\Masters\StakesTaxesMController');
			
		 

			 
			
		});


	});
});
