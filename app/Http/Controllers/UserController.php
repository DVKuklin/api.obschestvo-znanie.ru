<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // http://127.0.0.1:8000/api/auth
    // {
    //     "name":"Коля",
    //     "password":"1111"
    // }
    // public function authUser(Request $request) {

    //     $users = User::select('id','password')->where('name',$request->name)->get();

    //     $user_id = null;

    //     for ($i=0;$i<count($users);$i++) {
    //         if (Hash::check($request->password, $users[$i]->password)) {
    //             $user_id = $users[$i]->id;
    //             break;
    //         }
    //     }



    //     // $res = User::where('name',$request->name)
    //     //     ->where('password',$request->password)
    //     //     ->first();

        
      
    //     if ($user_id != null) {
    //         $token = Str::random(80);
    //         $res = User::where('id',$user_id)->update(['custom_token'=>$token]);
    //         if ($res) {
    //             return response()->json([
    //                 'status'=>'success',
    //                 'token'=>$token
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'status'=>'error',
    //                 'message'=>'Something went wrong (.'
    //             ]);
    //         }

    //     } else {
    //         return response()->json([
    //             'status'=>'notfound'
    //         ]);           
    //     }
    // }

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
}
