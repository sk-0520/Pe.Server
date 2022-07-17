<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

use PeServer\Core\Uuid;
use PeServer\Core\Cryptography;


abstract class UserUtility
{
	public static function generateSignupToken(): string
	{
		return Cryptography::generateRandomBinary(40)->toHex();
	}

	public static function generateUserId(): string
	{
		return Uuid::generateGuid();
	}
}
