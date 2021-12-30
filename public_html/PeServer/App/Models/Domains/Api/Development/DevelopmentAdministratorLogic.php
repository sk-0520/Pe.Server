<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Api\Development;

use PeServer\Core\Uuid;
use \PeServer\Core\Mime;
use \Deploy\ScriptArgument;
use \PeServer\Core\ILogger;
use PeServer\Core\Database;
use \PeServer\Core\Log\Logging;
use \PeServer\Core\Mvc\LogicBase;
use \PeServer\Core\Mvc\ActionResponse;
use \PeServer\Core\HttpStatusCode;
use PeServer\App\Models\UserLevel;
use PeServer\App\Models\UserState;
use \PeServer\Core\Mvc\LogicCallMode;
use \PeServer\Core\Mvc\LogicParameter;
use \PeServer\Core\Throws\CoreException;
use \PeServer\App\Models\AppConfiguration;
use \PeServer\App\Models\Domains\Api\ApiLogicBase;
use PeServer\App\Models\Database\Entities\UsersEntityDao;
use PeServer\App\Models\Database\Entities\UserAuthenticationsEntityDao;

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
		$loginId = 'root';
		$password = 'root';

		$params = [
			'user_id' => 'ffffffff-ffff-4fff-ffff-ffffffffffff',
			'login_id' => $loginId,
			'password' => password_hash($password, PASSWORD_DEFAULT),
			'user_name' => "user-$loginId",
			'email' => "$loginId@localhost",
			'website' => 'http://localhost',
		];

		$database = $this->openDatabase();

		$result = $database->transaction(function (Database $database, $params) {
			$usersEntityDao = new UsersEntityDao($database);
			$userAuthenticationsEntityDao = new UserAuthenticationsEntityDao($database);

			$usersEntityDao->insertUser(
				$params['user_id'],
				$params['login_id'],
				UserLevel::ADMINISTRATOR,
				UserState::ENABLED,
				$params['user_name'],
				$params['email'],
				$params['website'],
				'開発用 自動生成 管理者'
			);

			$userAuthenticationsEntityDao->insertUserAuthentication(
				$params['user_id'],
				'',
				$params['password']
			);

			$database->update("update users set state = :state where level = :level", [
				'state' => UserState::DISABLED,
				'level' => UserLevel::SETUP,
			]);

			return true;
		}, $params);


		$response = ActionResponse::json([
			'success' => $result
		]);
		$this->setResponse($response);
	}
}
