<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\User\RegisterUserRequest;
use App\Http\Requests\User\LoginUserRequest;
use App\Http\Requests\User\HandleSocialLoginRequest;
use App\Services\User\UserService;

class UserController extends Controller
{
  public function __construct(protected UserService $userService)
  {
  }

  public function register(RegisterUserRequest $request) : JsonResponse
  {
    $result = $this->userService->register($request->all());
    return response()->json($result, 200);
  }

  public function login(LoginUserRequest $request) : JsonResponse
  {
    $result = $this->userService->login($request->all());
    return response()->json($result, 200);
  }

  public function handleSocialLogin(HandleSocialLoginRequest $request) : JsonResponse
  {
    $result = $this->userService->handleSocialLogin($request->all());
    return response()->json($result, 200);
  }

  public function info(Request $request) : JsonResponse
  {
    $user = Auth::user();
    return response()->json($user, 200);
  }
}
