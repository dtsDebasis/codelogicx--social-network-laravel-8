<?php

namespace App\Http\Controllers\Api;

use Exception;
use Validator;
use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * Route :
     * Details : Register user API
     * Author : Debasis Chakraborty
     * Created On : 18 th June 2021
     * Updated On : 18 th June 2021
     * Last Update By : Debasis Chakraborty
     */

    public function register(Request $request)
    {
        try {
            //Validation Check
            $validation = Helper::check_param($request->all(), [
                'name' => 'required|min:3',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
            ]);

            if ($validation !== true) {
                return $validation;
            }

            //If validation check pass create new user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);

            if ($user) {
                //If user created successfully create auth token and send response
                $token = $user->createToken('AuthToken')->accessToken;
                return Helper::rj('Registration Successful', 200, [
                    'token' => $token,
                ]);
            }

            //For any unexpected Error send 400
            return Helper::rj('Something Bad happened', 400, []);
        } catch (Exception $e) {
			return Helper::rj($e->getMessage(), 500);
		}
    }

    /**
     * Route :
     * Details : Login user API
     * Author : Debasis Chakraborty
     * Created On : 18 th June 2021
     * Updated On : 18 th June 2021
     * Last Update By : Debasis Chakraborty
     */

    public function login(Request $request)
    {
        try {
            //Validation Check
            $validation = Helper::check_param($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);

            if ($validation !== true) {
                return $validation;
            }

            $credentials = [
                'email' => $request->email,
                'password' => $request->password
            ];
            if (auth()->attempt($credentials)) {
                $token = auth()->user()->createToken('AuthToken')->accessToken;
                return Helper::rj('login Successful', 200, [
                    'token' => $token,
                ]);
            } else {
                return Helper::rj('Unauthorised', 401, []);
            }

            return Helper::rj('Something Bad happened', 400, []);
        } catch (Exception $e) {
			return Helper::rj($e->getMessage(), 500);
		}
    }
}
