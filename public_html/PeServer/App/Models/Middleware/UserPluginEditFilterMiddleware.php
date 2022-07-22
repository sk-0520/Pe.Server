<?php

declare(strict_types=1);

namespace PeServer\App\Models\Middleware;

use PeServer\Core\Uuid;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\HttpRequest;
use PeServer\App\Models\AppDatabase;
use PeServer\App\Models\SessionManager;
use PeServer\Core\Http\HttpRequestExists;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\App\Models\Dao\Entities\PluginsEntityDao;


final class UserPluginEditFilterMiddleware implements IMiddleware
{
	public final function handleBefore(MiddlewareArgument $argument): MiddlewareResult
	{
		$pluginIdState = $argument->request->exists('plugin_id');
		if ($pluginIdState->exists && $pluginIdState->kind === HttpRequestExists::KIND_URL) {
			$pluginId = $argument->request->getValue($pluginIdState->name);
			// ここにきてるってことはユーザーフィルタを通過しているのでセッションを見る必要はないけど一応ね
			if (Uuid::isGuid($pluginId) && SessionManager::existsAccount()) {
				$pluginId = Uuid::adjustGuid($pluginId);
				$account = SessionManager::getAccount();
				$database = AppDatabase::open();
				$pluginsEntityDao = new PluginsEntityDao($database);
				if ($pluginsEntityDao->selectIsUserPlugin($pluginId, $account->userId)) {
					return MiddlewareResult::none();
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
