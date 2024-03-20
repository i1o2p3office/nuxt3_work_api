<?php

namespace App\Services\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

use App\Repositorys\UserRepository;
use App\Models\User;

class UserService
{
	private string $apiUrl = "";

	public function __construct(protected UserRepository $userRepository)
	{
		$this->apiUrl = config('app.url');
	}

	/**
	 * 註冊並返回登入token
	 */
	public function register(array $request): array
	{
		$model = $request;
		$model['loginType'] = 1;
		$model['password'] = Hash::make($request['password']);
		$uid = $this->userRepository->register($model);
		return $this->login($request, 1);
	}

	/**
	 * 登入並返回token
	 */
	public function login(array $request, int $loginType = 1): array
	{
		$client = $this->userRepository->getLoginClient();
		$user = $this->userRepository->getMemberByEmail($loginType, $request['email']);

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

	/**
	 * 處理三方登入
	 */
	public function handleSocialLogin(array $request): array
	{
		$data = Cache::store('redis')->get($request['token']);

		if (!$data) {
			throw new \Exception('social login token expired');
		}

		// 移除token
		Cache::store('redis')->forget($request['token']);
		// 取得user
		$data = json_decode($data, true);
		$check = $this->userRepository->getMemberByThirdPartyId((int)$data['loginType'], $data['id']);

		// 密碼設定為id，因passport email需要和本地區分
		$data['password'] = $data['id'];
		$data['email'] = $data['email'] . "||" . $data['loginType'];
		if ($check) {
			return $this->login($data, $check->loginType);
		} else {
			$registerDat = [
				'loginType' => (int)$data['loginType'],
				'name' => $data['name'],
				'email' => $data['email'],
				'avatar' => $data['avatar'],
				'thirdPartyId' => $data['id'],
				'password' => Hash::make($data['password']),
			];
			$uid = $this->userRepository->register($registerDat);
			return $this->login($data, $data['loginType']);
		}
	}
}
