<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\App\Models\AppErrorHandler;
use PeServer\App\Models\SessionKey;
use PeServer\Core\DI\IDiRegisterContainer;
use PeServer\Core\Environment;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Mvc\IResponsePrinterFactory;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Mvc\RouteRequest;
use PeServer\Core\Mvc\RouteSetting;
use PeServer\Core\Mvc\Route;
use PeServer\Core\Store\StoreOptions;
use PeServer\Core\Store\Stores;

class AppRoute extends Route
{
	/**
	 * 生成。
	 *
	 * @param RouteSetting $routeSetting
	 * @param Stores $stores
	 */
	public function __construct(RouteRequest $routeRequest, RouteSetting $routeSetting, Stores $stores, Environment $environment, IResponsePrinterFactory $responsePrinterFactory, ILoggerFactory $loggerFactory, IDiRegisterContainer $serviceLocator)
	{
		parent::__construct($routeRequest, $routeSetting, $stores, $environment, $responsePrinterFactory, $loggerFactory, $serviceLocator);
	}
}
