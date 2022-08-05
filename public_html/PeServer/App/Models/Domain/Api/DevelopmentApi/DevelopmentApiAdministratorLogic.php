<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Api\DevelopmentApi;

use PeServer\Core\Mime;
use PeServer\Core\Cryptography;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\AppCryptography;
use PeServer\App\Models\Domain\UserLevel;
use PeServer\App\Models\Domain\UserState;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\App\Models\Domain\Api\ApiLogicBase;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\Dao\Entities\UserAuthenticationsEntityDao;

class DevelopmentApiAdministratorLogic extends ApiLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NONE
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$users = [
			[
				'user_id' => 'ffffffff-ffff-4fff-ffff-ffffffffffff',
				'login_id' => 'root',
				'password' => 'root',
				'email' => 'root@localhost.localdomain',
				'level' => UserLevel::ADMINISTRATOR,
				'note' => '開発用 自動生成 管理者'
			],
			[
				'user_id' => 'eeeeeeee-eeee-4eee-eeee-eeeeeeeeeeee',
				'login_id' => 'zap-admin',
				'password' => 'zap-admin',
				'email' => 'zap-admin@localhost.localdomain',
				'level' => UserLevel::ADMINISTRATOR,
				'note' => 'ZAP用 自動生成 管理者'
			],
			[
				'user_id' => 'dddddddd-dddd-4ddd-dddd-dddddddddddd',
				'login_id' => 'zap-user',
				'password' => 'zap-user',
				'email' => 'zap-user@localhost.localdomain',
				'level' => UserLevel::USER,
				'note' => 'ZAP用 自動生成 ユーザー'
			],
		];

		$params = array_map(function ($i) {
			return [
				'user_id' => $i['user_id'],
				'login_id' => $i['login_id'],
				'password' => Cryptography::toHashPassword($i['password']),
				'user_name' => 'user-' . $i['login_id'],
				'level' => $i['level'],
				'email' => AppCryptography::encrypt($i['email']),
				'mark_email' => AppCryptography::toMark($i['email']),
				'website' => 'http://localhost',
				'description' => $i['note'],
				'note' => $i['note'],
			];
		}, $users);

		$database = $this->openDatabase();

		$result = $database->transaction(function (IDatabaseContext $context) use($params) {
			$usersEntityDao = new UsersEntityDao($context);
			$userAuthenticationsEntityDao = new UserAuthenticationsEntityDao($context);

			foreach ($params as $user) {

				$usersEntityDao->insertUser(
					$user['user_id'],
					$user['login_id'],
					$user['level'],
					UserState::ENABLED,
					$user['user_name'],
					$user['email'],
					(int)$user['mark_email'],
					$user['website'],
					$user['description'],
					$user['note']
				);

				$userAuthenticationsEntityDao->insertUserAuthentication(
					$user['user_id'],
					'',
					$user['password']
				);
			}

			$context->update("update users set state = :state where level = :level", [
				'state' => UserState::DISABLED,
				'level' => UserLevel::SETUP,
			]);

			return true;
		});

		$this->setResponseJson(ResponseJson::success([
			'success' => $result,
		]));
	}
}
