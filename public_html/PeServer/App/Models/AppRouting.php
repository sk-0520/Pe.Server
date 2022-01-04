<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\Route;
use PeServer\Core\Routing;
use PeServer\Core\RequestPath;
use PeServer\Core\RouteSetting;
use PeServer\Core\IMiddleware;
use PeServer\Core\Store\StoreOption;
use PeServer\Core\Store\CookieOption;
use PeServer\Core\Store\SessionOption;
use PeServer\App\Models\SessionManager;
use PeServer\Core\Store\TemporaryOption;

class AppRouting extends Routing
{
	/**
	 * 生成。
	 *
	 * @param RouteSetting $routeSetting
	 * @param StoreOption $storeOption
	 */
	public function __construct(RouteSetting $routeSetting, StoreOption $storeOption)
	{
		parent::__construct($routeSetting, $storeOption);

		SessionManager::initialize($this->session);
	}

	public function execute(string $requestMethod, RequestPath $requestPath): void
	{
		(new AppErrorHandler($requestPath))->register();

		parent::execute($requestMethod, $requestPath);
	}
}
