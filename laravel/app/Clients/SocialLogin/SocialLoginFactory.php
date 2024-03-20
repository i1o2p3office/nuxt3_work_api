<?php
// SocialLoginFactory.php
namespace App\Clients\SocialLogin;

use App\Clients\SocialLogin\Client\LineClient;
use App\Clients\SocialLogin\Client\FacebookClient;
use App\Clients\SocialLogin\Client\GoogleClient;
use App\Clients\SocialLogin\Client\SocialLoginClient;
use App\Repositorys\SocialLoginConfigRepository;

class SocialLoginFactory
{
  public function __construct(protected SocialLoginConfigRepository $socialLoginConfigRepository)
  {
  }

  public function create($loginType) : SocialLoginClient
  {
    // 取得登入參數
    $config = $this->socialLoginConfigRepository->getSocialLoginConfig($loginType);
    $parameters = json_decode($config->parameters, true);
    $apiUrl = json_decode($config->apiUrl, true);

    switch ($loginType) {
      case 2:
        return new GoogleClient($parameters, $apiUrl);
      case 3:
        return new FacebookClient($parameters, $apiUrl);
      case 4:
        return new LineClient($parameters, $apiUrl);
      default:
        throw new \Exception("Invalid social login type: $type");
    }
  }
}