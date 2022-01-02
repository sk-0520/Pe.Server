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

	private static ?IActionFilter $csrf = null;
	public static function csrf(): IActionFilter
	{
		return self::$csrf ??= new class extends Csrf implements IActionFilter
		{
			public function filtering(FilterArgument $argument): FilterResult
			{
				$result = $argument->request->exists(self::REQUEST_KEY);
				if (!$result['exists']) {
					$argument->logger->warn('要求CSRFトークンなし');
					return FilterResult::error(HttpStatus::forbidden());
				}

				$requestToken = $argument->request->getValue(self::REQUEST_KEY);
				if ($argument->session->tryGet(self::SESSION_KEY, $sessionToken)) {
					if ($requestToken === $sessionToken) {
						return FilterResult::none();
					}
					$argument->logger->warn('CSRFトークン不一致');
				} else {
					$argument->logger->warn('セッションCSRFトークンなし');
				}

				return FilterResult::error(HttpStatus::forbidden());
			}
		};
	}
}
