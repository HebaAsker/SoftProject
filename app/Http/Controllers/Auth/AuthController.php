<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    use ImageTrait;

    public function register(Request $request)
    {
        $fields = $request->validate(
            [
                'name' => 'required|string',
                'email' =>'required | email | unique:users,email',
                'password' =>'required | string | confirmed',
                'phone' => 'required |string | unique:users,phone',
                'address' => 'required|string',
                'is_worker' => 'required',
            ]);

            $file_name = $this->saveImage($request->image, 'images/users');


            $user = User::create(
                [
                    'name' => $fields ['name'],
                    'email' => $fields ['email'],
                    'password' => bcrypt($fields ['password']),
                    'phone' => $fields ['phone'],
                    'address' => $fields ['address'],
                    'is_worker' => $fields ['is_worker'],
                    'job' => $request->input('job'),
                    'image' => $file_name,
                ]);

            $token = $user->createToken('SoftProject')->plainTextToken;
            $response =
            [
                'user' => $user,
                'token' => $token
            ];

            return response($response,201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return ['message' => 'Logged out successfully'];
    }

    public function login(Request $request)
    {
        $fields = $request->validate(
            [
                'email' =>'required | email',
                'password' =>'required | string',
            ]);

                //Check email
                $user = User::where('email', $fields['email'])->first();

                //Check password
              if(!$user || !Hash::check($fields['password'], $user->password))
              {
                return response(['message' => 'Email or password not correct'] , 401);
              }

            $token = $user->createToken('SoftProject')->plainTextToken;
            $response =
            [
                'user' => $user,
                'token' => $token
            ];

            return response($response,201);
    }

    public function update(User $user ,Request $request)
    {
        $fields = $request->validate(
            [
                'name' => 'required|string',
                'email' =>'required | email | unique:users,email',
                'password' =>'required | string | confirmed',
                'phone' => 'required |string | unique:users,phone',
                'address' => 'required|string',
                'is_worker' => 'required',
            ]);

            $file_name = $this->saveImage($request->image, 'images/users');


            $user = Auth::user();
            $user->name = $fields ['name'];
            $user->email =  $fields ['email'];
            $user->password =  bcrypt($fields ['password']);
            $user->phone =  $fields ['phone'];
            $user->address =  $fields ['address'];
            $user->is_worker =  $fields ['is_worker'];
            $user->job=$request->input('job');
            $user->image=$file_name;
            $user->save();

            $response =
            [
                'user' => $user,
            ];

            return response($response,201);
    }

}
