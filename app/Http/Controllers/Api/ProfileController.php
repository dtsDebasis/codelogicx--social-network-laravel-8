<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{

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
