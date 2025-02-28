<?php

namespace App\Http\Controllers\Api\Auth;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Api\Auth\LoginRequest;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request)
    {
        try {

            $credentials = $request->only('email', 'password');

            throw_if(! Auth::attempt($credentials), new Exception(__('messages.login_error'), 30001));

            $user = Auth::user();

            return $this->success(__('messages.login_success'), 30101, [
                'access_token' => $user->createToken('access_token')->plainTextToken,
                'user' => new UserResource($user)
            ]);

        } catch (Exception $e) {
            
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
