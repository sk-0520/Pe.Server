<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

use PeServer\Core\I18n;

abstract class UserLevel
{
	public const USER = 'user';
	public const SETUP = 'setup';
	public const ADMINISTRATOR = 'administrator';

	public static function toString(string $userLevel): string
	{
		return match ($userLevel) {
			self::USER => I18n::message('enum/user_level/user'),
			self::SETUP => I18n::message('enum/user_level/setup'),
			self::ADMINISTRATOR => I18n::message('enum/user_level/administrator'),
		};
	}
}
