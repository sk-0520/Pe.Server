<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\Routing;
use PeServer\Core\RouteSetting;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\Store\StoreOption;
use PeServer\App\Models\SessionManager;
use PeServer\App\Models\AppErrorHandler;
use PeServer\Core\Store\SpecialStore;

class AppRouting extends Routing
{
	/**
	 * 生成。
	 *
	 * @param RouteSetting $routeSetting
	 * @param StoreOption $storeOption
	 */
	public function __construct(HttpMethod $httpMethod, RequestPath $requestPath, RouteSetting $routeSetting, SpecialStore $special, StoreOption $storeOption)
	{
		parent::__construct($httpMethod, $requestPath, $routeSetting, $special, $storeOption);

		SessionManager::initialize($this->session);
	}

	public function execute(): void
	{
		(new AppErrorHandler($this->requestPath))->register();

		parent::execute();
	}
}
