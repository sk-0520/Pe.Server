<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Middleware;

use PeServer\Core\Collections\Arr;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;
use PeServer\Core\Regex;
use PeServer\Core\Security;
use PeServer\Core\Throws\NotImplementedException;

/**
 * CSRFミドルウェア。
 */
class CsrfMiddleware implements IMiddleware
{
	#region

	protected const MODE_HEADER = 0;
	protected const MODE_BODY = 1;

	#endregion

	#region variable

	protected Regex $regex;

	#endregion

	public function __construct(
		private ILogger $logger,
	) {
		$this->regex = new Regex();
	}

	#region function

	/**
	 * CSRFトークン不正時のHTTP応答ステータス。
	 *
	 * @return HttpStatus
	 */
	protected function getErrorHttpStatus(): HttpStatus
	{
		return HttpStatus::MisdirectedRequest;
	}

	protected function getRequestMode(MiddlewareArgument $argument): int
	{
		if ($this->regex->isMatch($argument->requestPath->full, '/\Aajax\//')) {
			return self::MODE_HEADER;
		}

		return self::MODE_BODY;
	}

	function handleBeforeHeader(MiddlewareArgument $argument): MiddlewareResult
	{
		if (!$argument->request->httpHeader->existsHeader(Security::CSRF_HEADER_NAME)) {
			$this->logger->warn('要求CSRFトークンなし');
			return MiddlewareResult::error($this->getErrorHttpStatus(), 'CSRF');
		}

		if ($argument->stores->session->tryGet(Security::CSRF_SESSION_KEY, $sessionToken)) {
			$values = $argument->request->httpHeader->getValues(Security::CSRF_HEADER_NAME);
			if (Arr::getCount($values) === 1) {
				if ($values[0] === $sessionToken) {
					return MiddlewareResult::none();
				}
				$this->logger->error('CSRFトークン不一致');
			} else {
				$this->logger->error('CSRFトークン数異常');
			}
		} else {
			$this->logger->warn('セッションCSRFトークンなし');
		}


		return MiddlewareResult::error($this->getErrorHttpStatus(), 'CSRF');
	}

	function handleBeforeBody(MiddlewareArgument $argument): MiddlewareResult
	{
		$result = $argument->request->exists(Security::CSRF_REQUEST_KEY);
		if (!$result->exists) {
			$this->logger->warn('要求CSRFトークンなし');
			return MiddlewareResult::error($this->getErrorHttpStatus(), 'CSRF');
		}

		$requestToken = $argument->request->getValue(Security::CSRF_REQUEST_KEY);
		if ($argument->stores->session->tryGet(Security::CSRF_SESSION_KEY, $sessionToken)) {
			if ($requestToken === $sessionToken) {
				return MiddlewareResult::none();
			}
			$this->logger->error('CSRFトークン不一致');
		} else {
			$this->logger->warn('セッションCSRFトークンなし');
		}

		return MiddlewareResult::error($this->getErrorHttpStatus(), 'CSRF');
	}

	#endregion

	#region IMiddleware

	public function handleBefore(MiddlewareArgument $argument): MiddlewareResult
	{
		return match ($this->getRequestMode($argument)) {
			self::MODE_HEADER => $this->handleBeforeHeader($argument),
			self::MODE_BODY => $this->handleBeforeBody($argument),
			default => throw new NotImplementedException(),
		};
	}

	public final function handleAfter(MiddlewareArgument $argument, HttpResponse $response): MiddlewareResult
	{
		return MiddlewareResult::none();
	}

	#endregion
}
