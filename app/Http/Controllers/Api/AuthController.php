<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Class AuthController
 * @package App\Http\Controllers\Api
 */
class AuthController extends Controller
{
    /**
     * @var int
     */
    public $successStatus = 200;

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(),
            [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
                'c_password' => 'required|same:password',
            ]);

        if ($validator->fails())
        {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken(env("APP_NAME"))->accessToken;
        return response()->json(['success' => $success], $this->successStatus);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')]))
        {
            $user = Auth::user();
            $success['token'] =  $user->createToken(env("APP_NAME"))->accessToken;

            return response()->json(['success' => $success], $this->successStatus);
        }
        else
        {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser()
    {
        $user = Auth::user();

        if(empty($user))
            return response()->json(['error' => 'Not Found'], 500);

        return response()->json(['success' => $user], $this->successStatus);
    }
}