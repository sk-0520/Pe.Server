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

	private static ?IMiddleware $middleware = null;
	public static function middleware(): IMiddleware
	{
		return self::$middleware ??= new class extends Csrf implements IMiddleware
		{
			public function handle(MiddlewareArgument $argument): MiddlewareResult
			{
				$result = $argument->request->exists(self::REQUEST_KEY);
				if (!$result['exists']) {
					$argument->logger->warn('要求CSRFトークンなし');
					return MiddlewareResult::error(HttpStatus::forbidden(), 'CSRF');
				}

				$requestToken = $argument->request->getValue(self::REQUEST_KEY);
				if ($argument->session->tryGet(self::SESSION_KEY, $sessionToken)) {
					if ($requestToken === $sessionToken) {
						return MiddlewareResult::none();
					}
					$argument->logger->warn('CSRFトークン不一致');
				} else {
					$argument->logger->warn('セッションCSRFトークンなし');
				}

				return MiddlewareResult::error(HttpStatus::forbidden(), 'CSRF');
			}
		};
	}
}
