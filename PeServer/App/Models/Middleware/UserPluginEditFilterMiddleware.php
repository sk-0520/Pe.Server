<?php

declare(strict_types=1);

namespace PeServer\App\Models\Middleware;

use PeServer\App\Models\AppDatabase;
use PeServer\App\Models\Dao\Entities\PluginsEntityDao;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\HttpRequestExists;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;
use PeServer\Core\Uuid;

final class UserPluginEditFilterMiddleware implements IMiddleware
{
	public function __construct(
		private IDatabaseConnection $connection
	) {
	}

	//[IMiddleware]

	final public function handleBefore(MiddlewareArgument $argument): MiddlewareResult
	{
		$pluginIdState = $argument->request->exists('plugin_id');
		if ($pluginIdState->exists && $pluginIdState->kind === HttpRequestExists::KIND_URL) {
			$pluginId = $argument->request->getValue($pluginIdState->name);
			// ここにきてるってことはユーザーフィルタを通過しているのでセッションを見る必要はないけど一応ね
			if (Uuid::isGuid($pluginId) && $argument->stores->session->tryGet(SessionKey::ACCOUNT, $account)) {
				$pluginId = Uuid::adjustGuid($pluginId);
				$database = $this->connection->open();
				$pluginsEntityDao = new PluginsEntityDao($database);
				/** @var \PeServer\App\Models\Data\SessionAccount $account */
				if ($pluginsEntityDao->selectIsUserPlugin($pluginId, $account->userId)) {
					return MiddlewareResult::none();
				}
			}
		}

		return MiddlewareResult::error(HttpStatus::NotFound);
	}

	public function handleAfter(MiddlewareArgument $argument, HttpResponse $response): MiddlewareResult
	{
		return MiddlewareResult::none();
	}
}
