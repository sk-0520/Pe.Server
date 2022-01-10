<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Api\Development;

use PeServer\Core\Mime;
use PeServer\Core\Uuid;
use PeServer\Core\ILogger;
use \Deploy\ScriptArgument;
use PeServer\Core\Log\Logging;
use PeServer\Core\Cryptography;
use PeServer\Core\Mvc\LogicBase;
use PeServer\Core\Http\HttpStatusCode;
use PeServer\Core\Database\Database;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\ActionResponse;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Throws\CoreException;
use PeServer\App\Models\AppCryptography;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Domains\UserLevel;
use PeServer\App\Models\Domains\UserState;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\App\Models\Domains\Api\ApiLogicBase;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\Dao\Entities\UserAuthenticationsEntityDao;
use PeServer\Core\Mvc\DataContent;

class DevelopmentAdministratorLogic extends ApiLogicBase
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
		$emailDomain = '@localhost.localdomain';
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
				'note' => $i['note'],
			];
		}, $users);

		$database = $this->openDatabase();

		$result = $database->transaction(function (IDatabaseContext $context, $params) {
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
					$user['mark_email'],
					$user['website'],
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
		}, $params);


		$this->setContent(Mime::JSON, [
			'success' => $result
		]);
	}
}
