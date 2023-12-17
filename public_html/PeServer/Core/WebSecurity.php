<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\MiddlewareArgument;
use PeServer\Core\Throws\SessionException;

class WebSecurity
{
	#region define

	public const CSRF_KIND_SESSION_KEY = 1;
	public const CSRF_KIND_REQUEST_ID = 2;
	public const CSRF_KIND_REQUEST_NAME = 3;
	public const CSRF_KIND_HEADER_NAME = 4;

	private const CSRF_SESSION_KEY = 'core__csrf';
	private const CSRF_REQUEST_ID = 'core__csrf_id';
	private const CSRF_REQUEST_NAME = 'core__csrf_name';
	private const CSRF_HEADER_NAME = 'X-CSRF-TOKEN';

	private const CSRF_HASH_ALGORITHM = 'sha256';

	#endregion

	#region function

	/**
	 * CSRFに関する項目名等々を取得。
	 *
	 * HTTP/HTML上のキーに関するのものが対象となる。
	 *
	 * @param int $kind
	 * @phpstan-param self::CSRF_KIND_* $kind
	 * @return non-empty-string
	 */
	public function getCsrfKind(int $kind): string
	{
		return match ($kind) {
			self::CSRF_KIND_SESSION_KEY => self::CSRF_SESSION_KEY,
			self::CSRF_KIND_REQUEST_ID => self::CSRF_REQUEST_ID,
			self::CSRF_KIND_REQUEST_NAME => self::CSRF_REQUEST_NAME,
			self::CSRF_KIND_HEADER_NAME => self::CSRF_HEADER_NAME,
		};
	}

	/**
	 * CSRFトークンのハッシュアルゴリズム。
	 * @return non-empty-string
	 */
	protected function getCsrfTokenHash(): string
	{
		return self::CSRF_HASH_ALGORITHM;
	}

	/**
	 * CSRFトークンを取得。
	 *
	 * @return non-empty-string
	 * @throws SessionException セッションID取得失敗。
	 */
	public function generateCsrfToken(): string
	{
		$sessionId = session_id();
		if ($sessionId === false) {
			throw new SessionException('セッションID取得失敗');
		}

		$algorithm = $this->getCsrfTokenHash();
		$hash = Cryptography::generateHashString($algorithm, new Binary($sessionId));
		if(Text::isNullOrEmpty($hash)) {
			throw new SessionException('CSRFトークン生成失敗');
		}

		return $hash;
	}

	#endregion
}
