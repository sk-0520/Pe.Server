<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains;

use PeServer\App\Models\Database\Entities\UsersEntityDao;
use \PeServer\Core\Database;
use \PeServer\Core\TrueKeeper;
use \PeServer\Core\Mvc\Validator;
use \PeServer\Core\StringUtility;
use \PeServer\Core\Mvc\ValidationReceivable;

class AccountValidator
{
	public const LOGIN_ID_LENGTH = 50;
	public const PASSWORD_RANGE_MIN = 8;
	public const PASSWORD_RANGE_MAX = 50;
	public const USER_NAME_LENGTH = 100;
	public const EMAIL_LENGTH = 254;
	public const WEBSITE_LENGTH = 2083;

	private ValidationReceivable $_receiver;
	private Validator $_validator;

	public function __construct(ValidationReceivable $receiver, Validator $validator)
	{
		$this->_receiver = $receiver;
		$this->_validator = $validator;
	}

	public function isLoginId(string $key, ?string $value): bool
	{
		if ($this->_validator->isNotWhiteSpace($key, $value)) {
			// @phpstan-ignore-next-line isNotWhiteSpace
			$value = StringUtility::trim($value);
			$trueKeeper = new TrueKeeper();

			$trueKeeper->state = $this->_validator->inLength($key, self::LOGIN_ID_LENGTH, $value);
			$trueKeeper->state = $this->_validator->isMatch($key, '/^[a-zA-Z0-9\\-\\._]+$/', $value);

			return $trueKeeper->state;
		}

		return false;
	}

	public function isPassword(string $key, ?string $value): bool
	{
		if ($this->_validator->isNotWhiteSpace($key, $value)) {
			/** @var string */
			$value = $value;
			$trueKeeper = new TrueKeeper();

			$trueKeeper->state = $this->_validator->inRange($key, self::PASSWORD_RANGE_MIN, self::PASSWORD_RANGE_MAX, $value);
			$trueKeeper->state = $this->_validator->isMatch($key, '/^[a-zA-Z0-9!-~]+$/', $value);

			return $trueKeeper->state;
		}

		return false;
	}

	public function isUserName(string $key, ?string $value): bool
	{
		if ($this->_validator->isNotWhiteSpace($key, $value)) {
			// @phpstan-ignore-next-line isNotWhiteSpace
			$value = StringUtility::trim($value);
			$trueKeeper = new TrueKeeper();

			$trueKeeper->state = $this->_validator->inLength($key, self::USER_NAME_LENGTH, $value);

			return $trueKeeper->state;
		}

		return false;
	}

	public function isEmail(string $key, ?string $value): bool
	{
		if ($this->_validator->isNotWhiteSpace($key, $value)) {
			// @phpstan-ignore-next-line isNotWhiteSpace
			$value = StringUtility::trim($value);
			$trueKeeper = new TrueKeeper();

			$trueKeeper->state = $this->_validator->inLength($key, self::EMAIL_LENGTH, $value);
			$trueKeeper->state = $this->_validator->isEmail($key, $value);

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

			$trueKeeper->state = $this->_validator->inLength($key, self::WEBSITE_LENGTH, $value);
			$trueKeeper->state = $this->_validator->isWebsite($key, $value);

			return $trueKeeper->state;
		}

		return true;
	}

	public function isFreeLoginId(Database $database, string $key, string $loginId): bool
	{
		$usersEntityDao = new UsersEntityDao($database);

		if ($usersEntityDao->selectExistsLoginId($loginId)) {
			$this->_receiver->receiveErrorMessage($key, 'ログインIDが使用できません');
			return false;
		}

		return true;
	}
}
