<?php

namespace App\Http\Controllers\Api;

use Exception;
use Validator;
use App\Models\User;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FriendshipController extends Controller
{
    /**
     * Route : http://127.0.0.1:8000/api/v1/send_friend_request
     * Method : POST
     * Details : Send Friend Request
     * Author : Debasis Chakraborty
     * Created On : 19 th June 2021
     * Updated On : 19 th June 2021
     * Last Update By : Debasis Chakraborty
     * @return \Illuminate\Http\JsonResponse
     */
    public function send_friend_request(Request $request)
    {
        try {
            //Validation Check
            $validation = Helper::check_param($request->all(), [
                'username' => 'required|min:3',
            ]);

            if ($validation !== true) {
                return $validation;
            }
            //Get Sender & recipient details
            $sender = auth()->user();
            $recipient = User::getUserByUsername($request->username);

            //Check recipient exists or not
            if ($recipient) {
                //Validate username is same or not
                if (User::isSame($sender->username,$recipient->username)) {
                    return Helper::rj('Username is same as yours', 422);
                }
                //Check if Sender has blocked Recipient
                if ($sender->hasBlocked($recipient)) {
                    return Helper::rj('You blocked recipient', 422);
                }
                //check if Sender is blocked by Recipient
                if ($sender->isBlockedBy($recipient)) {
                    return Helper::rj('Recipient blocked you', 422);
                }
                //Check if Sender is Friend with Recipient
                if ($sender->isFriendWith($recipient)) {
                    return Helper::rj('Already a friend', 422);
                }
                //Check if Sender has already sent a friend request to Recipient
                if ($sender->hasSentFriendRequestTo($recipient)) {
                    return Helper::rj('Friend request already sent', 422);
                }

                //Send Friend Request
                $sender->befriend($recipient);

                return Helper::rj('Friend request sent successfully', 200);
            }

            return Helper::rj('Not a valid username', 422);
        } catch (Exception $e) {
            return Helper::rj($e->getMessage(), 500);
        }
    }
}
