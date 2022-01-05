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
use PeServer\Core\HttpStatusCode;
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
		$email = "$loginId@localhost.localdomain";

		$params = [
			'user_id' => 'ffffffff-ffff-4fff-ffff-ffffffffffff',
			'login_id' => $loginId,
			'password' => Cryptography::toHashPassword($password),
			'user_name' => "user-$loginId",
			'email' => AppCryptography::encrypt($email),
			'mark_email' => AppCryptography::toMark($email),
			'website' => 'http://localhost',
		];

		$database = $this->openDatabase();

		$result = $database->transaction(function (IDatabaseContext $context, $params) {
			$usersEntityDao = new UsersEntityDao($context);
			$userAuthenticationsEntityDao = new UserAuthenticationsEntityDao($context);

			$usersEntityDao->insertUser(
				$params['user_id'],
				$params['login_id'],
				UserLevel::ADMINISTRATOR,
				UserState::ENABLED,
				$params['user_name'],
				$params['email'],
				$params['mark_email'],
				$params['website'],
				'開発用 自動生成 管理者'
			);

			$userAuthenticationsEntityDao->insertUserAuthentication(
				$params['user_id'],
				'',
				$params['password']
			);

			$context->update("update users set state = :state where level = :level", [
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
