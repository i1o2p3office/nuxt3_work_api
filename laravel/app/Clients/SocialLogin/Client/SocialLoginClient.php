<?php

namespace App\Clients\SocialLogin\Client;

interface SocialLoginClient {
  /**
   * 取得登入網址
   */
  public function getLoginUrl(string $token, string $callbackUrl) : string;

  /**
   * 處理callback
   */
  public function handleCallbcak(array $request) : array;
}