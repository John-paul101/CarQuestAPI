<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */


     public function adminLogin(Request $request){
        $input = $request->all();

        // Validate the inputs
    $validator = Validator::make($request->all(), [
        'email' => 'required|string|email|max:254',
        'password' => 'required|string',
    ], [
        'email.required' => 'Email is Required for Login',
        'email.string' => 'Email must be a string',
        'email.email' => 'Email must be a valid email',
        'password.string' => 'Password must be a string',
        'password.required' => 'Password is Required for Login',
    ]);

    // Validation failed
    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
    }

    // Retrieve the user details
    $user = User::where('email', $request->email)->first();

    // Attempt authentication
    if (!$token = auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
        return response()->json(['success' => false, 'errors' => ['email' => ['Invalid email or password']]], 400);
    }

    //Retrieve user Details
    $user = auth()->user();

    // Generate new token data
    $tokenData = $this->createNewToken($token)->getData();

    // Return success response
    $response = [
    'success' => true,
    'message' => 'User logged in successfully.',
    'user' => [
        'accessToken' => $tokenData->accessToken,
        'email' => $user->email,
        'name' => $user->name,
        'is_admin' => $user->is_admin,
    ],
];
    return response()->json($response, 200);
}















    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'accessToken' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}
