<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

use PeServer\Core\TrueKeeper;
use PeServer\Core\Mvc\Validator;
use PeServer\Core\StringUtility;
use PeServer\Core\Mvc\IValidationReceiver;


abstract class ValidatorBase
{
	public const EMAIL_LENGTH = 254;
	public const WEBSITE_LENGTH = 2083;

	protected IValidationReceiver $receiver;
	protected Validator $validator;

	protected function __construct(IValidationReceiver $receiver, Validator $validator)
	{
		$this->receiver = $receiver;
		$this->validator = $validator;
	}

	public final function isEmail(string $key, ?string $value): bool
	{
		if ($this->validator->isNotWhiteSpace($key, $value)) {
			/** @var string $value isNotWhiteSpace */
			$trueKeeper = new TrueKeeper();

			$trueKeeper->state = $this->validator->inLength($key, self::EMAIL_LENGTH, $value);
			$trueKeeper->state = $this->validator->isEmail($key, $value);

			return $trueKeeper->state;
		}

		return true;
	}

	public final function isWebsite(string $key, ?string $value): bool
	{
		if (!StringUtility::isNullOrWhiteSpace($value)) {
			/** @var string $value isNotWhiteSpace */
			$trueKeeper = new TrueKeeper();

			$trueKeeper->state = $this->validator->inLength($key, self::WEBSITE_LENGTH, $value);
			$trueKeeper->state = $this->validator->isWebsite($key, $value);

			return $trueKeeper->state;
		}

		return true;
	}
}
