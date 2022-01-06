<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\MiddlewareArgument;
use PeServer\Core\Throws\SessionException;


abstract class Security
{
	public const CSRF_SESSION_KEY = 'core__csrf';
	public const CSRF_REQUEST_KEY = 'core__csrf';
	private const CSRF_HASH_ALGORITHM = 'sha256';

	/**
	 * CSRFトークンを取得。
	 *
	 * @return string
	 * @throws SessionException セッションID取得失敗。
	 */
	public static function generateCsrfToken(): string
	{
		$sessionId = session_id();
		if ($sessionId === false) {
			throw new SessionException('セッションID取得失敗');
		}

		$hash = hash(self::CSRF_HASH_ALGORITHM, $sessionId);

		return $hash;
	}
}
