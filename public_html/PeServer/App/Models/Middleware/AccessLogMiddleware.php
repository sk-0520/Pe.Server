<?php

declare(strict_types=1);

namespace PeServer\App\Models\Middleware;

use PeServer\App\Models\Domain\AccessLogManager;
use PeServer\Core\Mvc\Middleware\IShutdownMiddleware;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;

final class AccessLogMiddleware implements IShutdownMiddleware
{
	public function __construct(
		private AccessLogManager $accessLogManager
	) {
	}

	#region IShutdownMiddleware

	public function handleShutdown(MiddlewareArgument $argument): void
	{
		$this->accessLogManager->put();
	}

	#endregion
}
