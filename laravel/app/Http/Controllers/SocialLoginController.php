<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\SocialLogin\GetUrlRequest;
use App\Http\Requests\SocialLogin\CallbackRequest;
use App\Services\SocialLogin\SocialLoginService;

class SocialLoginController extends Controller
{
  public function __construct(protected SocialLoginService $socialLoginService)
  {
  }

  public function getSocialLoginUrl(GetUrlRequest $request) : JsonResponse
  {
    $result = $this->socialLoginService->getSocialLoginUrl($request->all());
    return response()->json($result, 200);
  }

  public function callback(CallbackRequest $request, string $gateway) : RedirectResponse
  {
    $loginType = 0;
    switch ($gateway) {
      case 'google':
        $loginType = 2;
        break;
      case 'facebook':
        $loginType = 3;
        break;
      case 'line':
        $loginType = 4;
        break;
    }

    $result = $this->socialLoginService->callback($request->all(), $loginType);
    // 轉址
    return redirect()->away($result['callbackUrl']);
  }
}
