<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\Routing;
use PeServer\Core\RouteSetting;
use PeServer\Core\Store\Stores;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\Store\StoreOptions;
use PeServer\App\Models\SessionManager;
use PeServer\App\Models\AppErrorHandler;

class AppRouting extends Routing
{
	/**
	 * 生成。
	 *
	 * @param RouteSetting $routeSetting
	 * @param Stores $stores
	 */
	public function __construct(HttpMethod $httpMethod, RequestPath $requestPath, RouteSetting $routeSetting, Stores $stores)
	{
		parent::__construct($httpMethod, $requestPath, $routeSetting, $stores);

		SessionManager::initialize($this->session);
	}

	public function execute(): void
	{
		(new AppErrorHandler($this->requestPath))->register();

		parent::execute();
	}
}
