<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\App\Models\AppErrorHandler;
use PeServer\App\Models\SessionKey;
use PeServer\Core\DI\IDiRegisterContainer;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Mvc\RouteRequest;
use PeServer\Core\Mvc\RouteSetting;
use PeServer\Core\Mvc\Routing;
use PeServer\Core\Store\StoreOptions;
use PeServer\Core\Store\Stores;

class AppRouting extends Routing
{
	/**
	 * 生成。
	 *
	 * @param RouteSetting $routeSetting
	 * @param Stores $stores
	 */
	public function __construct(RouteRequest $routeRequest, RouteSetting $routeSetting, Stores $stores, ILoggerFactory $loggerFactory, IDiRegisterContainer $serviceLocator)
	{
		parent::__construct($routeRequest, $routeSetting, $stores, $loggerFactory, $serviceLocator);

		//$loggerFactory = $serviceLocator->get(\PeServer\Core\Log\ILoggerFactory::class);
		// $logger->debug('DEBUG-ilogger!!');

		// $logger = $loggerFactory->create('asd');
		// $logger->debug('DEBUG-logfact!!');

		//SessionKey::initialize($this->stores->session);
	}

	// public function execute(): void
	// {
	// 	(new AppErrorHandler($this->requestPath))->register();

	// 	parent::execute();
	// }
}
