<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\CoreError;


abstract class Csrf
{
	public const SESSION_KEY = 'core__csrf';
	public const REQUEST_KEY = 'core__csrf';
	private const HASH_ALGORITHM = 'sha256';

	public static function generateToken(): string
	{
		$sessionId = session_id();
		if ($sessionId === false) {
			throw new CoreError();
		}

		$hash = hash(self::HASH_ALGORITHM, $sessionId);

		return $hash;
	}
}
