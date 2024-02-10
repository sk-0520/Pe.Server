<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

use PeServer\Core\I18n;
use PeServer\Core\Text;
use PeServer\Core\Throws\NotImplementedException;

abstract class UserLevel
{
	public const UNKNOWN = Text::EMPTY;
	public const USER = 'user';
	public const SETUP = 'setup';
	public const ADMINISTRATOR = 'administrator';

	public static function toString(string $userLevel): string
	{
		return match ($userLevel) {
			self::USER => I18n::message('enum/user_level/user'),
			self::SETUP => I18n::message('enum/user_level/setup'),
			self::ADMINISTRATOR => I18n::message('enum/user_level/administrator'),
			default => throw new NotImplementedException()
		};
	}
}
