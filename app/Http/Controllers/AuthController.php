<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Validator;

class AuthController extends BaseController
{
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
        // $this->middleware('auth:api', ['except' => ['postLogin', 'register']]);
    }

    public function _postLogin()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function mobileLogin()
    {
        $mobile = request(['mobile']);


        $data['login_mobile'] = $mobile;
        return $this->sendResponse($data, $this->successMsg);
    }

    public function mobileLoginOtp()
    {
        $mobile_otp = request(['mobile_otp']);


        $data['mobile_otp'] = $mobile_otp;
        return $this->sendResponse($data, $this->successMsg);
    }

    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email|max:255',
            'password' => 'required',
        ]);

        try {
            $credentials = request(['email', 'password']);
            if (!$token = $this->jwt->attempt($credentials)) {

                return response()->json(['user_not_found'], 404);
            }
            $user = auth()->user();
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], 500);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], 500);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent' => $e->getMessage()], 500);
        }
        $data['token'] = $token;
        $data['user'] = $user;
        return $this->sendResponse($data);
    }

    /**
     * Register api
     *
     * @return $data
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'mobile' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
        $input = $request->all();
        $input['password'] = app('hash')->make($input['password']);
        User::create($input);

        $data['token'] = $this->jwt->attempt(request(['email', 'password']));
        $data['user'] = auth()->user();
        return $this->sendResponse($data, $this->successMsg);
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer'
        ]);
    }
}
