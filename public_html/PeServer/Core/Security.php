<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\MiddlewareArgument;
use PeServer\Core\Throws\SessionException;


abstract class Security
{
	#region define

	public const CSRF_SESSION_KEY = 'core__csrf';
	public const CSRF_REQUEST_ID = 'core__csrf_id';
	public const CSRF_REQUEST_KEY = 'core__csrf_name';
	private const CSRF_HASH_ALGORITHM = 'sha256';
	public const CSRF_HEADER_NAME = 'X-CSRF-TOKEN';

	#endregion

	#region function

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

		$hash = Cryptography::generateHashString(self::CSRF_HASH_ALGORITHM, new Binary($sessionId));

		return $hash;
	}

	#endregion
}
