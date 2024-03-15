<?php

namespace App\Repositorys;

use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserRepository
{
    public function getMemberByEmail(string $email): object
    {
        return User::where('email', $email)->first();
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

    public function getLoginClient(): object
    {
        return DB::table('oauth_clients')
                ->where('name', 'login')
                ->first();
    }
}
