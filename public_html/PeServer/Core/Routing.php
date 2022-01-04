<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Exception;
use PeServer\Core\HttpStatus;
use PeServer\Core\Log\Logging;
use PeServer\Core\RequestPath;
use PeServer\Core\ArrayUtility;
use PeServer\Core\MiddlewareResult;
use PeServer\Core\RouteSetting;
use PeServer\Core\IMiddleware;
use PeServer\Core\MiddlewareArgument;
use PeServer\Core\Mvc\ActionResult;
use PeServer\Core\Mvc\ActionRequest;
use PeServer\Core\Mvc\IActionResult;
use PeServer\Core\Store\CookieStore;
use PeServer\Core\Store\StoreOption;
use PeServer\Core\Mvc\ControllerBase;
use PeServer\Core\Store\CookieOption;
use PeServer\Core\Store\SessionStore;
use PeServer\Core\Store\SessionOption;
use PeServer\Core\Store\TemporaryStore;
use PeServer\Core\Store\TemporaryOption;
use PeServer\Core\Mvc\ControllerArgument;

/**
 * ルーティング。
 */
class Routing
{
	/**
	 * ルーティング設定。
	 *
	 * @var RouteSetting
	 */
	protected RouteSetting $setting;

	protected CookieStore $cookie;
	protected TemporaryStore $temporary;
	protected SessionStore $session;

	protected ILogger $middlewareLogger;

	/**
	 * 生成。
	 *
	 * @param RouteSetting $routeSetting
	 * @param StoreOption $storeOption
	 */
	public function __construct(RouteSetting $routeSetting, StoreOption $storeOption)
	{
		$this->setting = $routeSetting;

		$this->cookie = new CookieStore($storeOption->cookie);
		$this->temporary = new TemporaryStore($storeOption->temporary, $this->cookie);
		$this->session = new SessionStore($storeOption->session, $this->cookie);

		$this->middlewareLogger = Logging::create('middleware');
	}

	/**
	 * ミドルウェア単独処理。
	 *
	 * @param RequestPath $requestPath
	 * @param ActionRequest $request
	 * @param IMiddleware|string $middleware
	 * @return bool 次のミドルウェアを実行してよいか
	 */
	private function handleMiddlewareCore(RequestPath $requestPath, ActionRequest $request, IMiddleware|string $middleware): bool
	{
		$middlewareArgument = new MiddlewareArgument($requestPath, $this->cookie, $this->session, $request, $this->middlewareLogger);
		if (is_string($middleware)) {
			/** @var IMiddleware */
			$middleware = new $middleware();
		}
		$middlewareResult = $middleware->handle($middlewareArgument);

		if ($middlewareResult->canNext()) {
			return true;
		}

		$middlewareResult->apply();
		return false;
	}

	/**
	 * ミドルウェアをグワーッと処理。
	 *
	 * @param array<IMiddleware|string> $middleware
	 * @param RequestPath $requestPath
	 * @param ActionRequest $request
	 * @return bool 後続処理は可能か
	 */
	private function handleMiddleware(array $middleware, RequestPath $requestPath, ActionRequest $request): bool
	{
		foreach ($middleware as $middlewareItem) {
			$canNext = $this->handleMiddlewareCore($requestPath, $request, $middlewareItem);
			if (!$canNext) {
				return false;
			}
		}

		return true;
	}

	/**
	 * アクション実行。
	 *
	 * @param RequestPath $requestPath
	 * @param string $rawControllerName
	 * @param string $methodName
	 * @param string[] $urlParameters
	 * @param array<IMiddleware|string> $middleware
	 * @return void
	 */
	private function executeAction(RequestPath $requestPath, string $rawControllerName, string $methodName, array $urlParameters, array $middleware): void
	{
		$splitNames = 	StringUtility::split($rawControllerName, '/');
		$controllerName = $splitNames[ArrayUtility::getCount($splitNames) - 1];

		$request = new ActionRequest($urlParameters);

		// アクション共通ミドルウェア処理
		if (!$this->handleMiddleware($this->setting->actionMiddleware, $requestPath, $request)) {
			return;
		}

		// アクションに紐づくミドルウェア処理
		if (!$this->handleMiddleware($middleware, $requestPath, $request)) {
			return;
		}

		$logger = Logging::create($controllerName);
		$controllerArgument = new ControllerArgument($this->cookie, $this->temporary, $this->session, $logger);

		/** @var ControllerBase */
		$controller = new $controllerName($controllerArgument);
		/** @var IActionResult */
		$actionResult = $controller->$methodName($request);
		$controller->output($actionResult);
	}

	/**
	 * メソッド・パスから登録されている処理を実行。
	 *
	 * 失敗時の云々が甘いというかまだなんも考えてない。
	 *
	 * @param string $requestMethod HttpMethod を参照。
	 * @param RequestPath $requestPath リクエストパス。
	 * @return void
	 */
	private function executeCore(string $requestMethod, RequestPath $requestPath): void
	{
		// グローバルミドルウェアの適用
		if (ArrayUtility::getCount($this->setting->globalMiddleware)) {
			$request = new ActionRequest([]);
			if (!$this->handleMiddleware($this->setting->globalMiddleware, $requestPath, $request)) {
				return;
			}
		}

		/** @var RouteAction|null */
		$errorAction = null;
		foreach ($this->setting->routes as $route) {
			$action = $route->getAction($requestMethod, $requestPath);
			if (!is_null($action)) {
				if ($action->status->code() === HttpStatus::none()->code()) {
					$this->executeAction($requestPath, $action->className, $action->classMethod, $action->params, $action->middleware);
					return;
				} else if (is_null($errorAction)) {
					$errorAction = $action;
				}
			}
		}

		if (is_null($errorAction)) {
			MiddlewareResult::error(HttpStatus::internalServerError())->apply();
		} else {
			MiddlewareResult::error($errorAction->status)->apply();
		}
	}

	/**
	 * メソッド・パスから登録されている処理を実行。
	 *
	 * 失敗時の云々が甘いというかまだなんも考えてない。
	 *
	 * @param string $requestMethod HttpMethod を参照。
	 * @param RequestPath $requestPath リクエストパス。
	 * @return void
	 */
	public function execute(string $requestMethod, RequestPath $requestPath): void
	{
		$this->executeCore($requestMethod, $requestPath);
	}
}
