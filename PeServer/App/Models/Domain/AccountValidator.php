<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

use PeServer\Core\I18n;
use PeServer\Core\TrueKeeper;
use PeServer\Core\Mvc\Validator;
use PeServer\Core\Text;
use PeServer\Core\Mvc\IValidationReceiver;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\App\Models\Domain\ValidatorBase;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;

class AccountValidator extends ValidatorBase
{
	public const LOGIN_ID_RANGE_MIN = 6;
	public const LOGIN_ID_RANGE_MAX = 50;
	public const PASSWORD_RANGE_MIN = 8;
	public const PASSWORD_RANGE_MAX = 50;
	public const USER_NAME_RANGE_MIN = 4;
	public const USER_NAME_RANGE_MAX = 100;
	public const USER_DESCRIPTION_LENGTH = 1000;

	public function __construct(IValidationReceiver $receiver, Validator $validator)
	{
		parent::__construct($receiver, $validator);
	}

	public function isLoginId(string $key, ?string $value): bool
	{
		if ($this->validator->isNotWhiteSpace($key, $value)) {
			$trueKeeper = new TrueKeeper();

			$trueKeeper->state = $this->validator->inRange($key, self::LOGIN_ID_RANGE_MIN, self::LOGIN_ID_RANGE_MAX, $value);
			$trueKeeper->state = $this->validator->isMatch($key, '/^[a-zA-Z0-9\\-\\._]+$/', $value);

			return $trueKeeper->state;
		}

		return false;
	}

	public function isPassword(string $key, ?string $value): bool
	{
		if ($this->validator->isNotWhiteSpace($key, $value)) {
			$trueKeeper = new TrueKeeper();

			$trueKeeper->state = $this->validator->inRange($key, self::PASSWORD_RANGE_MIN, self::PASSWORD_RANGE_MAX, $value);
			$trueKeeper->state = $this->validator->isMatch($key, '/^[a-zA-Z0-9!-~]+$/', $value);

			return $trueKeeper->state;
		}

		return false;
	}

	public function isUserName(string $key, ?string $value): bool
	{
		if ($this->validator->isNotWhiteSpace($key, $value)) {
			$trueKeeper = new TrueKeeper();

			$trueKeeper->state = $this->validator->inRange($key, self::USER_NAME_RANGE_MIN, self::USER_NAME_RANGE_MAX, $value);

			return $trueKeeper->state;
		}

		return false;
	}

	public function isDescription(string $key, ?string $value): bool
	{
		if (!Text::isNullOrWhiteSpace($value)) {
			$trueKeeper = new TrueKeeper();

			$trueKeeper->state = $this->validator->inLength($key, self::USER_DESCRIPTION_LENGTH, $value);

			return $trueKeeper->state;
		}

		return true;
	}


	public function isFreeLoginId(IDatabaseContext $context, string $key, string $loginId): bool
	{
		$usersEntityDao = new UsersEntityDao($context);

		if ($usersEntityDao->selectExistsLoginId($loginId)) {
			$this->receiver->receiveErrorMessage($key, I18n::message('error/unusable_login_id'));
			return false;
		}

		return true;
	}
}
