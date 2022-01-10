<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\Route;
use PeServer\Core\Routing;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\RouteSetting;
use PeServer\Core\IMiddleware;
use PeServer\Core\Store\StoreOption;
use PeServer\Core\Store\CookieOption;
use PeServer\Core\Store\SessionOption;
use PeServer\App\Models\SessionManager;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Store\TemporaryOption;

class AppRouting extends Routing
{
	/**
	 * 生成。
	 *
	 * @param RouteSetting $routeSetting
	 * @param StoreOption $storeOption
	 */
	public function __construct(HttpMethod $httpMethod, RequestPath $requestPath, RouteSetting $routeSetting, StoreOption $storeOption)
	{
		parent::__construct($httpMethod, $requestPath, $routeSetting, $storeOption);

		SessionManager::initialize($this->session);
	}

	public function execute(): void
	{
		(new AppErrorHandler($this->requestPath))->register();

		parent::execute();
	}
}
