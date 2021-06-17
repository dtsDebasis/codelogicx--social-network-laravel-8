<?php

namespace App\Http\Controllers\Api;

use Exception;
use Validator;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * Register user API
     * Author : Debasis Chakraborty
     * Created On : 18 th June 2021
     * Updated On : 18 th June 2021
     * Last Update By : Debasis Chakraborty
     */

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:3',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
            ]);

            if ($validator->fails()) {
                return Helper::rj('Validation Error', 422, $validator->errors());
            }

            return Helper::rj('Something Bad happened', 400, []);
        } catch (Exception $e) {
			return Helper::rj($e->getMessage(), 500);
		}
    }

    /**
     * Login user API
     * Author : Debasis Chakraborty
     * Created On : 18 th June 2021
     * Updated On : 18 th June 2021
     * Last Update By : Debasis Chakraborty
     */

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);

            if ($validator->fails()) {
                return Helper::rj('Validation Error', 422, $validator->errors());
            }

            return Helper::rj('Something Bad happened', 400, []);
        } catch (Exception $e) {
			return Helper::rj($e->getMessage(), 500);
		}
    }
}
