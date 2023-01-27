<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request) {

        $users = User::where('name', $request->name)->select('name','password','id')->get();

        if (count($users) == 0) {
            return [
                'status'=>'not_found',
                'message'=>'Такого пользователя в базе нет.'
            ];
        }

        $i = null;

        foreach ($users as $index => $user){
            if (Hash::check($request->password, $user->password) ){
                $i = $index;
            }
        }

        if ($i === null) {
            return [
                'status'=>'invalid_password',
                'message'=>'Неверный пароль.'
            ];
        }

        $users[$i]->tokens()->delete();
        $token = $users[$i]->createToken("tokenName");

        return [
            "status"=>"success",
            "message"=>"Вы авторизованы",
            'token' => $token->plainTextToken,
            'user_name' => $users[$i]->name
        ];
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();

        return "Вы разлогинилсь";
    }

    public function getUserName(Request $request) {
        return $request->user()->name;
    }

    public function isAuthenticated(Request $request) {
        if ($request->user()) {
            return [
                'status'=>'success',
                'message'=>'You are authenticated'
            ];
        }

        return [
            'status'=>'notAuth'
        ];
    }
}
