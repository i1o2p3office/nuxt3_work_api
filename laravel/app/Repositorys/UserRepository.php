<?php

namespace App\Repositorys;

use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserRepository
{
	public function getMemberByEmail(int $loginType, string $email): ?object
	{
		return User::where('email', $email)
							->where('loginType', $loginType)
							->first();
	}

	public function getMemberByThirdPartyId(int $loginType, string $thirdPartyId): ?object
	{
		return User::where('thirdPartyId', $thirdPartyId)
							->where('loginType', $loginType)
							->first();
	}

	public function register(array $model): int
	{
		try {
			DB::beginTransaction();

			$uid = User::insertGetId($model);

			DB::commit();
			return $uid;
		} catch (Exception $e) {
			DB::rollBack();
			return false;
		}
	}

	public function getLoginClient(): ?object
	{
		return DB::table('oauth_clients')
						->where('name', 'login')
						->first();
	}
}
