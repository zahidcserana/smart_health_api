<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\User;
use App\Models\DoctorDetail;
use Illuminate\Http\Request;
use Validator;

class AuthController extends BaseController
{
    public function _postLogin()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function delete($id)
    {
        User::destroy($id);
        return $this->sendResponse('ok');
    }

    public function mobileLogin(Request $request)
    {
        $mobile = $request->input('mobile');

        $user = User::where('mobile', $mobile)->first();
        if ($user) {
            $user->mobile_otp = mt_rand(1000, 9999);
            $user->save();
            $data['login_mobile'] = $mobile;
            $msg = 'OTP has been sent to your mobile: ' . $mobile . '. Please use this to login.';

            return $this->sendResponse($data, $msg);
        }
        return $this->sendError('Invalid mobile number.', 200);
    }

    public function mobileLoginOtp(Request $request)
    {
        $postData = $request->only(['mobile', 'mobile_otp']);

        $user = User::where('mobile', $postData['mobile'])->where('mobile_otp', $postData['mobile_otp'])->first();

        if ($user) {
            if (!$token = $this->jwt->fromUser($user)) {
                return response()->json(['user_not_found'], 404);
            }

            $data['token'] = $token;
            $data['user'] = $user;
            return $this->sendResponse($data);
        }

        return $this->sendError('Invalid Code!', 200);
    }

    public function postLogin(Request $request)
    {
        // $this->userContainer->login();
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
        $fromMobile = false;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'mobile' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), 200);
        }
        $input = $request->all();
        $input['password'] = app('hash')->make($input['password']);
        unset($input['password_confirmation']);
        try {
            $user = User::create($input);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return $this->sendError($msg, 200);
        }
        if ($fromMobile) {
            return $this->_otp($user);
        }

        $data['token'] = $this->jwt->attempt(request(['email', 'password']));
        $data['user'] = auth()->user();
        return $this->sendResponse($data, $this->successMsg);
    }

    public function _otp($user)
    {
        $user->mobile_otp = mt_rand(1000, 9999);
        $user->save();
        $data['login_mobile'] = $user->mobile;
        $msg = 'OTP has been sent to your mobile: ' . $user->mobile . '. Please use this to login.';

        return $this->sendResponse($data, $msg);
    }
    /**
     * Get the authenticated User.
     *
     * @return data
     */
    public function me()
    {
        $user = auth()->user();
        $user->doctor;
        $user['avatar'] = empty($user->picture) ? config('settings.user_pic') : $this->imageDir . $user->picture;
        return $this->sendResponse($user);
    }

    /**
     * Update user info
     *
     * @param Request name email mobile
     * @param int $id
     * @return data
     */
    public function update(Request $request, $id)
    {
        $data = $request->only(['name', 'email', 'mobile', 'city_id', 'area_id', 'address', 'blood_group', 'gender', 'user_type', 'video_calling_ID']);
        $user = User::with('doctor')->findOrfail($id);
        $user->update($data);
        $user['avatar'] = empty($user->picture) ? config('settings.user_pic') : $this->imageDir . $user->picture;
        if ($user->user_type == 'DOCTOR') {
            $this->_doctor($user, $request);
        }

        return $this->sendResponse($user, $this->successMsg);
    }

    private function _doctor($user, $request)
    {

        if ($user->doctor === null) {
            $user->doctor()->save(new DoctorDetail());
        } else if (!empty($request->input('doctor'))) {
            $user->doctor->update($request->input('doctor'));
        }
        $user->refresh();
        $user->doctor;

        return $user;
    }

    public function fileUpload(Request $request)
    {
        if ($request->hasFile('file')) {
            $user = User::findOrfail($request->input('id'));

            // $user = $request->auth;
            $file      = $request->file('file');
            $filename  = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $picture   = $user->id . date('YmdHis') . '-' . $extension;

            $dir = 'assets/images/' . $picture;
            $file->move('assets/images', $picture);

            $user->update(['picture' => $picture]);

            return $this->sendResponse(env('DOMAIN_NAME') . $dir, $this->successMsg);
        } else {
            return response()->json(["message" => "Select image first."]);
        }
    }

    public function _fileUpload(Request $request)
    {
        $data = $request->all();
        dd($data);
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
