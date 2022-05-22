<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Utils\ErrorType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function user()
    {
        $user = Auth::user();
        return jsend_success(new UserResource($user));
    }

    public function login(LoginUserRequest $request)
    {
        // return "Hi";
        $email = $request->input('email');
        $password = $request->input('password');
        $remember_me =  $request->input('remember_me');

        // try {
            $user = User::whereEmail($email)->first();

            if (is_null($user)) {
                return jsend_fail(['message' => 'User does not exists.'], JsonResponse::HTTP_UNAUTHORIZED);
            }

            if (!Auth::attempt(['email' => $email, 'password' => $password])) {
                return jsend_fail(['message' => 'Invalid Credentials.'], JsonResponse::HTTP_UNAUTHORIZED);
            }

            $user = Auth::user();

            $tokenResult = $user->createToken('IO Token');
            $access_token = $tokenResult->accessToken;
            $expiration = $tokenResult->token->expires_at->diffInSeconds(now());

            return jsend_success([
                'username' => $user->name,
                'email' => $user->email,
                'token_type' => 'Bearer',
                'access_token' => $access_token,
                'expires_in' => $expiration
            ]);
        // } catch (Exception $e) {
        //     Log::error('Login Failed!', [
        //         'code' => $e->getCode(),
        //         'trace' => $e->getTrace()
        //     ]);
        //     return jsend_error(['message' => 'Invalid Credentials']);

        // }
    }

    public function updateUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            // 'password' => 'required|string|confirmed',
            // 'password_confirmation' => 'required',
        ]);

        try {
            $user = Auth::user();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            if ($request->has('role')) {
                $user->role = $request->input('role');
            }
            // $user->password = Hash::make($request->input('password'));

            $user->save();
            return jsend_success(new UserResource($user), JsonResponse::HTTP_ACCEPTED);
        } catch (Exception $e) {
            return jsend_error(__('api.updated-failed', ['model' => 'User']), $e->getCode(), ErrorType::UPDATE_ERROR, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|confirmed',
            'new_password_confirmation' => 'required'
        ]);
        if (!(Hash::check($request->get('current_password'), Auth::user()->password))) {
            // The passwords matches            
            return jsend_fail(['message' => 'Your current password does not matches with the password.']);
        }
        if (strcmp($request->get('current_password'), $request->get('new_password')) == 0) {            // Current password and new password same            
            return jsend_fail(['message' => 'New Password cannot be same as your current password.']);
        }
        //Change Password        
        $user = Auth::user();
        $user->password = Hash::make($request->get('new_password'));
        $user->save();
        return jsend_success(['message' => 'Password successfully changed!'], JsonResponse::HTTP_CREATED);
    }





    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed',
            'password_confirmation' => 'required',
        ]);

        try {
            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            if ($request->has('role')) {
                $user->role = $request->input('role');
            }
            $user->password = Hash::make($request->input('password'));
            $user->save();
            return jsend_success(new UserResource($user), JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            return jsend_error(__('api.saved-failed', ['model' => 'User']), $e->getCode(), ErrorType::SAVE_ERROR, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return jsend_success(['message' => 'Successfully Logout.'], JsonResponse::HTTP_ACCEPTED);
    }
}
