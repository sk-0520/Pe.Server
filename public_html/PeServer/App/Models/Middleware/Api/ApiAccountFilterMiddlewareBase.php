<?php

declare(strict_types=1);

namespace PeServer\App\Models\Middleware\Api;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Cache\UserCacheItem;
use PeServer\App\Models\HttpHeaderName;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Collection\Arr;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;

abstract class ApiAccountFilterMiddlewareBase implements IMiddleware
{
	public function __construct(
		private AppDatabaseCache $dbCache
	) {
		//NOP
	}

	#region function

	/**
	 * アカウント用フィルタ処理。
	 *
	 * @param MiddlewareArgument $argument
	 * @return MiddlewareResult
	 */
	protected function filterCore(MiddlewareArgument $argument): MiddlewareResult
	{
		if ($argument->request->httpHeader->existsHeader(HttpHeaderName::API_KEY)) {
			if ($argument->request->httpHeader->existsHeader(HttpHeaderName::SECRET_KEY)) {
				$apiKeys = $argument->request->httpHeader->getValues(HttpHeaderName::API_KEY);
				$secrets = $argument->request->httpHeader->getValues(HttpHeaderName::SECRET_KEY);

				if (Arr::getCount($apiKeys) !== 1) {
					return MiddlewareResult::error(HttpStatus::Forbidden);
				}
				if (Arr::getCount($secrets) !== 1) {
					return MiddlewareResult::error(HttpStatus::Forbidden);
				}

				$apiKey = $apiKeys[0];
				$secret = $secrets[0];

				$userCache = $this->dbCache->readUserInformation();

				if (empty($userCache->items)) {
					return MiddlewareResult::error(HttpStatus::Forbidden);
				}

				$items = array_filter($userCache->items, fn (UserCacheItem $i) => $i->apiKey === $apiKey && $i->secret === $secret);
				if (Arr::getCount($items) !== 1) {
					return MiddlewareResult::error(HttpStatus::Forbidden);
				}

				$key = Arr::getFirstKey($items);
				$item =  $items[$key];
				return $this->filter($argument, $item);
			}
		}

		return MiddlewareResult::error(HttpStatus::Forbidden);
	}

	abstract protected function filter(MiddlewareArgument $argument, UserCacheItem $item): MiddlewareResult;

	#endregion

	#region IMiddleware

	final public function handleBefore(MiddlewareArgument $argument): MiddlewareResult
	{
		return $this->filterCore($argument);
	}

	final public function handleAfter(MiddlewareArgument $argument, HttpResponse $response): MiddlewareResult
	{
		return MiddlewareResult::none();
	}

	#endregion
}
