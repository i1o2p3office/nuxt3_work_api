<?php

namespace App\Services\SocialLogin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

use App\Clients\SocialLogin\SocialLoginFactory;

class SocialLoginService
{

	public function __construct(protected SocialLoginFactory $socialLoginFactory)
	{
	}

	/**
	 * 取得社群登入網址
	 */
	public function getSocialLoginUrl(array $request): array
	{
		$client = $this->socialLoginFactory->create($request['loginType']);

		// 此次登入 callback 檢查token
		$token = (string) Str::uuid();
		//Redis::set("social_login_$token", "true", 'ex', 300);
    Cache::store('redis')->put("social_login_$token", "true", 300); 
		$url = $client->getLoginUrl($token, $request['callbackUrl']);

		return ['url'=> $url];
	}

  /**
   * 社群登入 callback
   */
  public function callback(array $request, int $loginType) : array
  {
    $client = $this->socialLoginFactory->create($loginType);
    $userInfo = $client->handleCallbcak($request);
    $callbackUrl = $userInfo['callbackUrl'];

    // 暫存使用者資訊
    unset($userInfo['callbackUrl']);
    $token = (string) Str::uuid();
    $token = str_replace('-', '', $token);
    $json = json_encode($userInfo, JSON_UNESCAPED_UNICODE);
    Cache::store('redis')->put($token, $json, 30);

    return ['callbackUrl' => $callbackUrl.'?token='.$token];
  }
}
