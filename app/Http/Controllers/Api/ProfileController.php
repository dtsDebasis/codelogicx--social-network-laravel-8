<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
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
    public function me(Request $request)
    {
        try {
            //Validation Check
            $validation = Helper::check_param($request->all(), [
                'friends_limit' => 'required',
            ]);

            if ($validation !== true) {
                return $validation;
            }

            $me = auth()->user();
            //Get friends list
            $friends = $me->getFriends($request->friends_limit);
            return Helper::rj('Current User Details', 200, [
                "basic_info" => $me,
                "friends" => $friends,
            ]);
        } catch (Exception $e) {
            return Helper::rj($e->getMessage(), 500);
        }
    }
    /**
     * Route : http://127.0.0.1:8000/api/v1/public_profile
     * Method : POST
     * Auth Req as we have to return mutual friends
     * Details : Authenticated User Details
     * Author : Debasis Chakraborty
     * Created On : 18 th June 2021
     * Updated On : 18 th June 2021
     * Last Update By : Debasis Chakraborty
     * @return \Illuminate\Http\JsonResponse
     */
    public function public_profile(Request $request)
    {
        try {
            //Validation Check
            $validation = Helper::check_param($request->all(), [
                'username' => 'required|min:3',
                'mutual_friends_limit' => 'required',
            ]);

            if ($validation !== true) {
                return $validation;
            }
            $visitor = auth()->user();
            $user = User::getUserByUsername($request->username);
            //Check if user is valid
            if ($user) {
                //Validate username is same or not
                if (User::isSame($visitor->username,$user->username)) {
                    return Helper::rj('Username is same as yours', 422);
                }

                //Get mutual friend list
                $mutual_friends = $visitor->getMutualFriends($user, $request->mutual_friends_limit);
                return Helper::rj('Profile User Details', 200, [
                    "basic_info" => $user,
                    "mutual_friends" => $mutual_friends,
                ]);
            }
            return Helper::rj('Not a valid profile', 422);
        } catch (Exception $e) {
            return Helper::rj($e->getMessage(), 500);
        }
    }
    /**
     * Route : http://127.0.0.1:8000/api/v1/users?search=
     * Method : GET
     * Details : Search Users
     * Author : Debasis Chakraborty
     * Created On : 18 th June 2021
     * Updated On : 18 th June 2021
     * Last Update By : Debasis Chakraborty
     * @return \Illuminate\Http\JsonResponse
     */
    public function users(Request $request)
    {
        try {
            //Validation Check
            $validation = Helper::check_param($request->all(), [
                'search' => 'required|min:3',
            ]);

            if ($validation !== true) {
                return $validation;
            }
            $user = auth()->user();

            $columnsToSearch = ['name', 'email', 'username'];

            $searchQuery = '%' . $request->search . '%';

            $indents = User::where('id', 'LIKE', $searchQuery);

            foreach($columnsToSearch as $column) {
                $indents = $indents->orWhere($column, 'LIKE', $searchQuery);
            }

            //TODO : Pagination
            $indents = $indents->get();

            return Helper::rj('Results', 200,$indents);
        } catch (Exception $e) {
            return Helper::rj($e->getMessage(), 500);
        }
    }
}
