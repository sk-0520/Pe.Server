<?php

declare(strict_types=1);

namespace PeServer\App\Models\Middleware\Api;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Cache\UserCacheItem;
use PeServer\App\Models\Domain\UserLevel;
use PeServer\App\Models\Middleware\Api\ApiAccountFilterMiddlewareBase;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;

class ApiUserAccountFilterMiddleware extends ApiAccountFilterMiddlewareBase
{
	public function __construct(
		AppDatabaseCache $dbCache
	) {
		parent::__construct($dbCache);
	}

	//[ApiAccountFilterMiddlewareBase]

	protected function filter(MiddlewareArgument $argument, UserCacheItem $item): MiddlewareResult
	{
		if ($item->level !== UserLevel::USER) {
			return MiddlewareResult::error(HttpStatus::forbidden());
		}

		return MiddlewareResult::none();
	}
}
