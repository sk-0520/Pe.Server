<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Middleware;

use PeServer\Core\Uuid;
use PeServer\Core\HttpStatus;
use PeServer\Core\IMiddleware;
use PeServer\Core\MiddlewareResult;
use PeServer\App\Models\AppDatabase;
use PeServer\Core\Mvc\ActionRequest;
use PeServer\Core\MiddlewareArgument;
use PeServer\App\Models\SessionManager;
use PeServer\App\Models\Dao\Entities\PluginsEntityDao;


final class UserPluginEditFilterMiddleware implements IMiddleware
{
	public final function handle(MiddlewareArgument $argument): MiddlewareResult
	{
		$pluginIdState = $argument->request->exists('plugin_id');
		if ($pluginIdState['exists'] && $pluginIdState['type'] === ActionRequest::REQUEST_URL) {
			$pluginId = $argument->request->getValue('plugin_id');
			// ここにきてるってことはユーザーフィルタを通過しているのでセッションを見る必要はないけど一応ね
			if (Uuid::isGuid($pluginId) && SessionManager::hasAccount()) {
				$pluginId = Uuid::adjustGuid($pluginId);
				$account = SessionManager::getAccount();
				$database = AppDatabase::open();
				$pluginsEntityDao = new PluginsEntityDao($database);
				if ($pluginsEntityDao->selectIsUserPlugin($pluginId, $account['user_id'])) {
					return MiddlewareResult::none();
				}
			}
		}

		return MiddlewareResult::error(HttpStatus::notFound());
	}
}
