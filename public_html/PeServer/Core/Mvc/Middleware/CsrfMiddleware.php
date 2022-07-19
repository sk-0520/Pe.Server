<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Middleware;

use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;
use PeServer\Core\Security;

/**
 * CSRFミドルウェア。
 */
class CsrfMiddleware implements IMiddleware
{
	/**
	 * CSRFトークン不正時のHTTP応答ステータス。
	 *
	 * @return HttpStatus
	 */
	protected function getErrorHttpStatus(): HttpStatus
	{
		return HttpStatus::misdirected();
	}

	public function handleBefore(MiddlewareArgument $argument): MiddlewareResult
	{
		$result = $argument->request->exists(Security::CSRF_REQUEST_KEY);
		if (!$result->exists) {
			$argument->logger->warn('要求CSRFトークンなし');
			return MiddlewareResult::error($this->getErrorHttpStatus(), 'CSRF');
		}

		$requestToken = $argument->request->getValue(Security::CSRF_REQUEST_KEY);
		if ($argument->stores->session->tryGet(Security::CSRF_SESSION_KEY, $sessionToken)) {
			if ($requestToken === $sessionToken) {
				return MiddlewareResult::none();
			}
			$argument->logger->error('CSRFトークン不一致');
		} else {
			$argument->logger->warn('セッションCSRFトークンなし');
		}

		return MiddlewareResult::error($this->getErrorHttpStatus(), 'CSRF');
	}

	public final function handleAfter(MiddlewareArgument $argument): MiddlewareResult
	{
		return MiddlewareResult::none();
	}
}
