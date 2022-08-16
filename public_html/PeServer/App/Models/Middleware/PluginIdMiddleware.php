<?php

declare(strict_types=1);

namespace PeServer\App\Models\Middleware;

use PeServer\App\Models\AppDatabase;
use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Dao\Entities\PluginsEntityDao;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\HttpRequestExists;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;
use PeServer\Core\Uuid;


final class PluginIdMiddleware implements IMiddleware
{
	public function __construct(
		private AppDatabaseCache $dbCache
	) {
	}


	//[IMiddleware]

	public final function handleBefore(MiddlewareArgument $argument): MiddlewareResult
	{
		$pluginIdState = $argument->request->exists('plugin_id');
		if ($pluginIdState->exists && $pluginIdState->kind === HttpRequestExists::KIND_URL) {
			$pluginId = $argument->request->getValue($pluginIdState->name);
			// ここにきてるってことはユーザーフィルタを通過しているのでセッションを見る必要はないけど一応ね
			if (Uuid::isGuid($pluginId)) {
				$pluginId = Uuid::adjustGuid($pluginId);

				$plugins = $this->dbCache->readPluginInformation();
				foreach ($plugins as $plugin) {
					if (Uuid::isEqualGuid($plugin->pluginId, $pluginId)) {
						return MiddlewareResult::none();
					}
				}
			}
		}

		return MiddlewareResult::error(HttpStatus::notFound());
	}

	public function handleAfter(MiddlewareArgument $argument, HttpResponse $response): MiddlewareResult
	{
		return MiddlewareResult::none();
	}
}
