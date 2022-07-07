<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\Routing;
use PeServer\Core\RouteSetting;
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
	 * @param StoreOptions $storeOptions
	 */
	public function __construct(HttpMethod $httpMethod, RequestPath $requestPath, RouteSetting $routeSetting, StoreOptions $storeOptions)
	{
		parent::__construct($httpMethod, $requestPath, $routeSetting, $storeOptions);

		SessionManager::initialize($this->session);
	}

	public function execute(): void
	{
		(new AppErrorHandler($this->requestPath))->register();

		parent::execute();
	}
}
