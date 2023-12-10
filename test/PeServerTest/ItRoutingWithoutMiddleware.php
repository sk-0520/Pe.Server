<?php

declare(strict_types=1);

namespace PeServerTest;

use PeServer\App\Models\AppRouting;
use PeServer\Core\DI\IDiRegisterContainer;
use PeServer\Core\Environment;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\IResponsePrinterFactory;
use PeServer\Core\Http\ResponsePrinter;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Mvc\RouteRequest;
use PeServer\Core\Mvc\RouteSetting;
use PeServer\Core\Store\Stores;

class ItRoutingWithoutMiddleware extends AppRouting
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

	#region AppRouting

	protected function handleBeforeMiddleware(array $middleware, HttpRequest $request): bool
	{
		return true;
	}

	protected function handleAfterMiddleware(HttpRequest $request, HttpResponse $response): bool
	{
		return true;
	}

	protected function handleShutdownMiddleware(): void
	{
		//NOP
	}


	#endregion
}
