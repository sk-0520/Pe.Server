<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

use PeServer\Core\TrueKeeper;
use PeServer\Core\Mvc\Validator;
use PeServer\Core\Text;
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

	final public function isEmail(string $key, ?string $value): bool
	{
		if ($this->validator->isNotWhiteSpace($key, $value)) {
			$trueKeeper = new TrueKeeper();

			$trueKeeper->state = $this->validator->inLength($key, self::EMAIL_LENGTH, $value);
			$trueKeeper->state = $this->validator->isEmail($key, $value);

			return $trueKeeper->state;
		}

		return true;
	}

	final public function isWebsite(string $key, ?string $value): bool
	{
		if (!Text::isNullOrWhiteSpace($value)) {
			$trueKeeper = new TrueKeeper();

			$trueKeeper->state = $this->validator->inLength($key, self::WEBSITE_LENGTH, $value);
			$trueKeeper->state = $this->validator->isWebsite($key, $value);

			return $trueKeeper->state;
		}

		return true;
	}
}
