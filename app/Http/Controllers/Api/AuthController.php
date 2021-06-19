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
     * Route : http://127.0.0.1:8000/api/v1/register
     * Method : POST
     * Details : Register user API
     * Author : Debasis Chakraborty
     * Created On : 18 th June 2021
     * Updated On : 18 th June 2021
     * Last Update By : Debasis Chakraborty
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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
     * Route : http://127.0.0.1:8000/api/v1/login
     * Method : POST
     * Details : Login user API
     * Author : Debasis Chakraborty
     * Created On : 18 th June 2021
     * Updated On : 18 th June 2021
     * Last Update By : Debasis Chakraborty
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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
                return Helper::rj('UnAuthorised', 401, []);
            }

            return Helper::rj('Something Bad happened', 400, []);
        } catch (Exception $e) {
			return Helper::rj($e->getMessage(), 500);
		}
    }

    /**
     * Route : http://127.0.0.1:8000/api/v1/me
     * Method : GET
     * Details : Authenticated User Details
     * Author : Debasis Chakraborty
     * Created On : 18 th June 2021
     * Updated On : 18 th June 2021
     * Last Update By : Debasis Chakraborty
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        try {
            return Helper::rj('Current User Details', 200, auth()->user());
        } catch (Exception $e) {
            return Helper::rj($e->getMessage(), 500);
        }
    }
}
