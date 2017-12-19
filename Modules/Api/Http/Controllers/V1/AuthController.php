<?php

namespace Modules\Api\Http\Controllers\V1;

use App\Http\Controllers\BaseApiController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('api::index');
    }

    /**
     * User login and return userInfo.
     * @param  Request $request
     * @return Response
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {

            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {

            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json(compact('token'));

    }


    /**
     * Get auth info.
     * @return Response
     */
    public function me()
    {
        return view('api::create');
    }


    /**
     * User create.
     * @param  Request $request
     * @return Response
     */
    public function register(Request $request)
    {
        $user = [
            'username'=>$request->get('username'),
            'password'=>$request->get('password')
        ];

        $user = User::create($user);
        $token = \JWTAuth::fromUser($user);

        return response()->json(compact('token'));
    }


    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {


    }
}
