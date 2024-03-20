<?php
namespace App\Clients\SocialLogin\Client;

use App\Clients\SocialLogin\Client\SocialLoginClient;

class FacebookClient implements SocialLoginClient
{
  public function __construct(private array $parameters, private array $apiUrl)
  {
  }

  public function getLoginUrl(string $token) : string
  {
    
  }
}