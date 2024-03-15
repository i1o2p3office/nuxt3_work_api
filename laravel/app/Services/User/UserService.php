<?php

namespace App\Services\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Repositorys\UserRepository;
use App\Models\User;

class UserService
{
    private string $apiUrl = "";

    public function __construct(protected UserRepository $userRepository)
    {
        $this->apiUrl = config('app.url');
    }

    public function register(array $request): array
    {
        $model = $request;
        $model['loginType'] = 1;
        $model['password'] = Hash::make($request['password']);
        $uid = $this->userRepository->register($model);
        return $this->login($request);
    }

    public function login(array $request): array
    {
        $client = $this->userRepository->getLoginClient();
        $user = $this->userRepository->getMemberByEmail($request['email']);

        if (!$user) {
            throw new \Exception('User not found');
        }

        if ($user->loginType == 1) {
            if (Auth::attempt(['email' => $request['email'], 'password' => $request['password'], 'loginType' => 1]) == false) {
                throw new \Exception('email or password error');
            }
        }
        
        $response = Http::asForm()->post("{$this->apiUrl}/oauth/token", [
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'username' => $request['email'],
            'password' => $request['password'],
            'scope' => '',
        ]);

        return $response->json();
    }
}
