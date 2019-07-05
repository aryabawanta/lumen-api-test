<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;

class AuthController extends Controller
{
    public function registerOrUpdate(Request $request){
        $user_record = [
            'username' => $request->input('email'),
            'email' => $request->input('email'),
            'name' => $request->input('name'),
            'password' => Hash::make($request->input('password'))
        ];
        $record_checker = [
            'username' => $user_record['username'],
            'email' => $user_record['email']
        ];
        $register = User::updateOrCreate($record_checker, $user_record);

        $return = [
            'data' => [
                        'success' => true,
                        'message' => 'Successfully Register!',
                        'data' => $register
                    ],
            'status' => 200
        ];
        if (!$register)
            $return = [
                'data' => [
                    'success' => false,
                    'message' => 'Failed to Register!',
                    'data' => ''
                ],
                'status' => 400
            ];
        return response()->json($return['data'], $return['status']);
    }

    public function login(Request $request){
        $username = $request->input('username');
        $password = $request->input('password');

        $user = User::whereUsername($username)->first();

        $return = [
            'data'=> [
                    'success' => false,
                    'message' => 'Failed to Login!',
                    'data' => ''
                ],
            'status' => 400
        ];
        if ($this->_checkPasswordAndGenerateAPIToken($user, $password))
            $return = [
                'data'=> [
                    'success' => true,
                    'message' => 'Logged In',
                    'data' => $user
                ],
                'status' => 201
            ];
        return $return;
    }

    private function _checkPasswordAndGenerateAPIToken($user, $password){
        if (Hash::check($password, $user->password)) {
            $user->api_token = base64_encode(str_random(40));
            $user->save();

            return true;
        }

        return false;
    }
}
