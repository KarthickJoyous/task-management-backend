<?php

namespace App\Http\Controllers\Api\Auth;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Api\Auth\RegisterRequest;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(RegisterRequest $request)
    {
        try {

            $validated = $request->validated() + [
                'password' => Hash::make($request->password)
            ];

            $user = User::Create($validated);

            throw_if(! $user, new Exception(__('messages.register_error'), 10001));

            $data['access_token'] = $user->createToken('access_token')->plainTextToken;

            $data['user'] =  new UserResource($user);

            return $this->success(__('messages.register_success'), 10101, $data);

        } catch(Exception $e) {

            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
