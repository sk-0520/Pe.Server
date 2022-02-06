<?php

declare(strict_types=1);

namespace PeServer\App\Models\Middleware;

use PeServer\Core\Uuid;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\HttpRequest;
use PeServer\App\Models\AppDatabase;
use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\SessionManager;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\App\Models\Dao\Entities\PluginsEntityDao;


final class PluginIdMiddleware implements IMiddleware
{
	public final function handleBefore(MiddlewareArgument $argument): MiddlewareResult
	{
		$pluginIdState = $argument->request->exists('plugin_id');
		if ($pluginIdState['exists'] && $pluginIdState['type'] === HttpRequest::REQUEST_URL) {
			$pluginId = $argument->request->getValue('plugin_id');
			// ここにきてるってことはユーザーフィルタを通過しているのでセッションを見る必要はないけど一応ね
			if (Uuid::isGuid($pluginId)) {
				$pluginId = Uuid::adjustGuid($pluginId);

				$plugins = AppDatabaseCache::readPluginInformation();
				foreach($plugins as $plugin) {
					if(Uuid::isEqualGuid($plugin->pluginId, $pluginId)) {
						return MiddlewareResult::none();
					}
				}
			}
		}

		return MiddlewareResult::error(HttpStatus::notFound());
	}

	public function handleAfter(MiddlewareArgument $argument): MiddlewareResult
	{
		return MiddlewareResult::none();
	}
}
