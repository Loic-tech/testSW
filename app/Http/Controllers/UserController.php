<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;
use App\Http\Resources\UserCollection;


class UserController extends Controller
{
    public function create(Request $request) {
        $user = new User();

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));

        $user->save();
        return response()->json($user);
    }

    public function login(Request $request) {
        $login = $request->validate(
            [
                'email' => 'required|string',
                'password' => 'required|string'
            ]);

            
       if(!Auth::attempt($login)) {
            return response(['message' => 'Invalid login !']);
        }

        $accessToken = Auth::user()->createToken('auth-token')->plainTextToken;

        return response(['user' => Auth::user(), 'accessToken' => $accessToken]);
    }

    public function test(){

        $user=User::all();

       
        return response()->json(  UserCollection::collection($user));

    }
}
