<?php

declare(strict_types=1);

namespace PeServer\App\Models\Middleware\Api;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Cache\UserCacheItem;
use PeServer\App\Models\HttpHeaderName;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Collections\Arr;
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
		//NONE
	}

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
					return MiddlewareResult::error(HttpStatus::forbidden());
				}
				if (Arr::getCount($secrets) !== 1) {
					return MiddlewareResult::error(HttpStatus::forbidden());
				}

				$apiKey = $apiKeys[0];
				$secret = $secrets[0];

				$userCache = $this->dbCache->readUserInformation();

				if (empty($userCache->items)) {
					return MiddlewareResult::error(HttpStatus::forbidden());
				}

				$items = array_filter($userCache->items, fn (UserCacheItem $i) => $i->apiKey === $apiKey && $i->secret === $secret);
				if (Arr::getCount($items) !== 1) {
					return MiddlewareResult::error(HttpStatus::forbidden());
				}

				$key = Arr::getFirstKey($items);
				$item =  $items[$key];
				return $this->filter($argument, $item);
			}
		}

		return MiddlewareResult::error(HttpStatus::forbidden());
	}

	protected abstract function filter(MiddlewareArgument $argument, UserCacheItem $item): MiddlewareResult;

	//[IMiddleware]

	public final function handleBefore(MiddlewareArgument $argument): MiddlewareResult
	{
		return $this->filterCore($argument);
	}

	public final function handleAfter(MiddlewareArgument $argument, HttpResponse $response): MiddlewareResult
	{
		return MiddlewareResult::none();
	}
}
