<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains;

use \PeServer\Core\Mvc\Validator;
use \PeServer\Core\Mvc\ValidationReceivable;


class AccountValidator
{
	private ValidationReceivable $_receiver;
	private Validator $_validator;

	public function __construct(ValidationReceivable $receiver, Validator $validator)
	{
		$this->_receiver = $receiver;
		$this->_validator = $validator;
	}

	public function isLoginId(?string $value): bool
	{
		return false;
	}
}
