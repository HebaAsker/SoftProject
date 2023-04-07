<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * @return JsonResponse
    */
    //Get all users you can chat with
    public function index(): JsonResponse
    {
        $users = User::where('id', '!=', auth()->user()->id)->get();
        return $this->success($users);
    }
}
