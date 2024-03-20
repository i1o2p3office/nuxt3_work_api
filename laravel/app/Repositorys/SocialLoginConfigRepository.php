<?php

namespace App\Repositorys;

use App\Models\SocialLoginConfig;

class SocialLoginConfigRepository
{
    public function getSocialLoginConfig(int $loginType) : ?object
    {
        return SocialLoginConfig::where('loginType', $loginType)->first();
    }
}
