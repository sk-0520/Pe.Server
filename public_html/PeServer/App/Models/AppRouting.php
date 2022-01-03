<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\Routing;
use PeServer\Core\RequestPath;
use PeServer\Core\IActionFilter;
use PeServer\Core\Route;
use PeServer\Core\Store\CookieOption;
use PeServer\Core\Store\TemporaryOption;
use PeServer\Core\Store\SessionOption;
use PeServer\App\Models\SessionManager;

class AppRouting extends Routing
{
	/**
	 * 生成。
	 *
	 * @param array{global_filters:IActionFilter[],action_filters:IActionFilter[],routes:Route[]} $routeSetting
	 * @param array{cookie:CookieOption,temporary:TemporaryOption,session:SessionOption} $storeOption
	 */
	public function __construct(array $routeSetting, array $storeOption)
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
