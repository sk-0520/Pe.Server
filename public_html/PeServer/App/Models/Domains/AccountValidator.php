<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains;

use PeServer\App\Models\Database\Entities\UsersEntityDao;
use \PeServer\Core\Database;
use \PeServer\Core\TrueKeeper;
use \PeServer\Core\Mvc\Validator;
use \PeServer\Core\StringUtility;
use \PeServer\Core\Mvc\IValidationReceiver;

class AccountValidator
{
	public const LOGIN_ID_RANGE_MIN = 6;
	public const LOGIN_ID_RANGE_MAX = 50;
	public const PASSWORD_RANGE_MIN = 8;
	public const PASSWORD_RANGE_MAX = 50;
	public const USER_NAME_RANGE_MIN = 4;
	public const USER_NAME_RANGE_MAX = 100;
	public const EMAIL_LENGTH = 254;
	public const WEBSITE_LENGTH = 2083;

	private IValidationReceiver $receiver;
	private Validator $validator;

	public function __construct(IValidationReceiver $receiver, Validator $validator)
	{
		$this->receiver = $receiver;
		$this->validator = $validator;
	}

	public function isLoginId(string $key, ?string $value): bool
	{
		if ($this->validator->isNotWhiteSpace($key, $value)) {
			// @phpstan-ignore-next-line isNotWhiteSpace
			$value = StringUtility::trim($value);
			$trueKeeper = new TrueKeeper();

			$trueKeeper->state = $this->validator->inRange($key, self::LOGIN_ID_RANGE_MIN, self::LOGIN_ID_RANGE_MIN, $value);
			$trueKeeper->state = $this->validator->isMatch($key, '/^[a-zA-Z0-9\\-\\._]+$/', $value);

			return $trueKeeper->state;
		}

		return false;
	}

	public function isPassword(string $key, ?string $value): bool
	{
		if ($this->validator->isNotWhiteSpace($key, $value)) {
			/** @var string */
			$value = $value;
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
			// @phpstan-ignore-next-line isNotWhiteSpace
			$value = StringUtility::trim($value);
			$trueKeeper = new TrueKeeper();

			$trueKeeper->state = $this->validator->inRange($key, self::USER_NAME_RANGE_MIN, self::USER_NAME_RANGE_MAX, $value);

			return $trueKeeper->state;
		}

		return false;
	}

	public function isEmail(string $key, ?string $value): bool
	{
		if ($this->validator->isNotWhiteSpace($key, $value)) {
			// @phpstan-ignore-next-line isNotWhiteSpace
			$value = StringUtility::trim($value);
			$trueKeeper = new TrueKeeper();

			$trueKeeper->state = $this->validator->inLength($key, self::EMAIL_LENGTH, $value);
			$trueKeeper->state = $this->validator->isEmail($key, $value);

			return $trueKeeper->state;
		}

		return true;
	}

	public function isWebsite(string $key, ?string $value): bool
	{
		if (!StringUtility::isNullOrWhiteSpace($value)) {
			// @phpstan-ignore-next-line isNullOrWhiteSpace
			$value = StringUtility::trim($value);
			$trueKeeper = new TrueKeeper();

			$trueKeeper->state = $this->validator->inLength($key, self::WEBSITE_LENGTH, $value);
			$trueKeeper->state = $this->validator->isWebsite($key, $value);

			return $trueKeeper->state;
		}

		return true;
	}

	public function isFreeLoginId(Database $database, string $key, string $loginId): bool
	{
		$usersEntityDao = new UsersEntityDao($database);

		if ($usersEntityDao->selectExistsLoginId($loginId)) {
			$this->receiver->receiveErrorMessage($key, 'ログインIDが使用できません');
			return false;
		}

		return true;
	}
}
