<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

use PeServer\Core\Uuid;
use PeServer\Core\Cryptography;


abstract class UserUtility
{
	public static function generateSignupToken(): string
	{
		return Cryptography::generateRandomString(80);
	}

	public static function generateUserId(): string
	{
		return Uuid::generateGuid();
	}

	public static function generatePasswordReminderToken(): string
	{
		return Cryptography::generateRandomString(80);
	}
}
