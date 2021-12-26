<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains;

use \PeServer\Core\Mvc\Validator;
use \PeServer\Core\TrueKeeper;
use \PeServer\Core\Mvc\ValidationReceivable;


class AccountValidator
{
	private ValidationReceivable $_receiver; // @phpstan-ignore-line
	private Validator $_validator;

	public function __construct(ValidationReceivable $receiver, Validator $validator)
	{
		$this->_receiver = $receiver;
		$this->_validator = $validator;
	}

	public function isLoginId(string $key, ?string $value): bool
	{
		if ($this->_validator->isNotWhiteSpace($key, $value)) {
			/** @var string */
			$value = $value;
			$trueKeeper = new TrueKeeper();

			$trueKeeper->state = $this->_validator->inLength($key, 10, $value);
			$trueKeeper->state = $this->_validator->isMatch($key, '/^[a-zA-Z0-9]+$/', $value);
			return $trueKeeper->state;
		}

		return false;
	}
}
