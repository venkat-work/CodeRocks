<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use Carbon\Carbon;

class AuthController extends Controller
{
    public $successStatus = 200;
  
    public function register(Request $request) {    
        $validator = Validator::make($request->all(), [ 
                  'name' => 'required|string',
                  'email' => 'required|string|email|unique:users',
                  'password' => 'required|string',  
                  'c_password' => 'required|same:password', 
        ]);   
        if ($validator->fails()) {          
           return response()->json(['error'=>$validator->errors()], 401);                        
        }    
        $input = $request->all();  
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input); 
        //$success['token'] =  $user->createToken('AppName')->accessToken;
        return response()->json(['success'=>"User created successfully"], $this->successStatus); 
    }


    /*public function login(){ 
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('AppName')-> accessToken; 
            return response()->json(['success' => $success], $this-> successStatus); 
        } else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    } */

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        $credentials = request(['email', 'password']);
        
        if(!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'User authorization failed'
            ], 401);
        }
        $user = Auth::user();
        $tokenResult = $user->createToken('ApiPassToken');
        $token = $tokenResult->token;

        if ($request->remember_me){
            $token->expires_at = Carbon::now()->addWeeks(1);
        }

        $token->save();
        
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'user_name' => $user->name,
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }

    public function getUser() {
        $user = Auth::user();
        return response()->json(['success' => $user], $this->successStatus); 
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        $authUser = $request->user();
        $user = User::with('roles.permissions')->where('id', '=', $authUser->id)->firstOrFail();
        return response()->json($user);
    }
  
}
