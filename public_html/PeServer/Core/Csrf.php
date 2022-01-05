<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\MiddlewareArgument;
use PeServer\Core\Throws\SessionException;


abstract class Csrf
{
	public const SESSION_KEY = 'core__csrf';
	public const REQUEST_KEY = 'core__csrf';
	private const HASH_ALGORITHM = 'sha256';

	/**
	 * CSRFトークンを取得。
	 *
	 * @return string
	 * @throws SessionException セッションID取得失敗。
	 */
	public static function generateToken(): string
	{
		$sessionId = session_id();
		if ($sessionId === false) {
			throw new SessionException('セッションID取得失敗');
		}

		$hash = hash(self::HASH_ALGORITHM, $sessionId);

		return $hash;
	}
}
