<?php

declare(strict_types=1);

namespace PeServerTest;

use PeServer\App\Models\AppRouting;
use PeServer\Core\DI\IDiRegisterContainer;
use PeServer\Core\Http\IResponsePrinterFactory;
use PeServer\Core\Http\ResponsePrinter;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Mvc\RouteRequest;
use PeServer\Core\Mvc\RouteSetting;
use PeServer\Core\Store\Stores;

class TestRoutingWithoutMiddleware extends AppRouting
{
	/**
	 * 生成。
	 *
	 * @param RouteSetting $routeSetting
	 * @param Stores $stores
	 */
	public function __construct(RouteRequest $routeRequest, RouteSetting $routeSetting, Stores $stores, IResponsePrinterFactory $responsePrinterFactory, ILoggerFactory $loggerFactory, IDiRegisterContainer $serviceLocator)
	{
		parent::__construct($routeRequest, $routeSetting, $stores, $responsePrinterFactory, $loggerFactory, $serviceLocator);
	}
}