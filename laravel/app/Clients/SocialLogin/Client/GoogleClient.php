<?php
namespace App\Clients\SocialLogin\Client;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Clients\SocialLogin\Client\SocialLoginClient;

/**
 * 使用Google OAuth2.0
 */
class GoogleClient implements SocialLoginClient
{
  private string $redirectUri;

  public function __construct(private array $parameters, private array $apiUrl)
  {
    $this->redirectUri = config('app.url') . '/api/socialLogin/google/callback';
  }

  // 取得登入網址
  public function getLoginUrl(string $token, string $callbackUrl) : string
  {
    return "{$this->apiUrl['get_url']}?" .
            "scope=https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email&" .
            "access_type=offline&" .
            "include_granted_scopes=true&" .
            "response_type=code&" .
            "state=$token|$callbackUrl&" .
            "redirect_uri={$this->redirectUri}&" .
            "client_id={$this->parameters['client_id']}";
  }

  // 處理callback
  public function handleCallbcak(array $request) : array
  {
    $code = $request['code'];
    $state = $request['state'];
    $stateArr = explode('|', $state);
    $token = $stateArr[0];
    $callbackUrl = $stateArr[1];

    // 檢查token
    if (!Cache::store('redis')->get("social_login_$token")) {
      throw new \Exception('token 過期');
    }

    // 取得access token
    $tokenData = $this->getAccessToken($code);
    $accessToken = $tokenData['access_token'];

    // 取得使用者資訊
    $userInfo = $this->getUserInfo($accessToken);

    return [
      'id' => $userInfo['id'],
      'name' => $userInfo['name'],
      'email' => $userInfo['email'],
      'avatar' => $userInfo['picture'],
      'loginType' => 2,
      'callbackUrl' => $callbackUrl,
    ];
  }

  // 取得access token
  private function getAccessToken(string $code) : array
  {
    $response = Http::asForm()->post($this->apiUrl['get_token'], [
      'code' => $code,
      'client_id' => $this->parameters['client_id'],
      'client_secret' => $this->parameters['client_secret'],
      'redirect_uri' => $this->redirectUri,
      'grant_type' => 'authorization_code',
    ]);

    if (!$response->successful()) {
      $log = "\nGoogle get token\n" . "response: \n" . json_encode($response->json(), JSON_UNESCAPED_UNICODE) . "\n";
      Log::channel('apiLog')->info($log);
      throw new \Exception('三方登入失敗');
    }

    return $response->json();
  }

  // 取得使用者資訊
  private function getUserInfo(string $accessToken) : array
  {
    $response = Http::withToken($accessToken)->get($this->apiUrl['get_user']);

    if (!$response->successful()) {
      $log = "\nGoogle get user\n" . "response: \n" . $response->json() . "\n";
      Log::channel('apiLog')->info($log);
      throw new \Exception('三方登入失敗');
    }

    return $response->json();
  }
}
