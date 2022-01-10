<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains;

use PeServer\Core\Uuid;
use PeServer\Core\Cryptography;


abstract class UserUtility
{
	public static function generateSignupToken(): string
	{
		return Cryptography::generateRandomBytes(40)->toHex();
	}

	public static function generateUserId(): string
	{
		return Uuid::generateGuid();
	}
}
