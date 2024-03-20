<?php
namespace App\Clients\SocialLogin\Client;

use App\Clients\SocialLogin\Client\SocialLoginClient;

class LineClient implements SocialLoginClient
{
  public function __construct(private array $parameters, private array $apiUrl)
  {
  }

  public function getLoginUrl(string $token) : string
  {
    
  }
}