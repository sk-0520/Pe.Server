<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppCryptography;
use PeServer\Core\Cryptography;

abstract class ApiUtility
{
	private const API_KEY_LENGTH = 64;
	private const SECRET_LENGTH = 256;
	private const HTTP_HEAD_STRING = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz_!-~^|[],.';

	public static function generateKey(): string
	{
		return Cryptography::generateRandomString(self::API_KEY_LENGTH, self::HTTP_HEAD_STRING);
	}

	public static function generateSecret(): string
	{
		return Cryptography::generateRandomString(self::SECRET_LENGTH, self::HTTP_HEAD_STRING);
	}
}
