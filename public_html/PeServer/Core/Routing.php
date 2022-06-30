<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Log\Logging;
use PeServer\Core\ArrayUtility;
use PeServer\Core\RouteSetting;
use PeServer\Core\ActionSetting;
use PeServer\Core\Http\HttpHeader;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Mvc\IActionResult;
use PeServer\Core\Store\CookieStore;
use PeServer\Core\Store\StoreOption;
use PeServer\Core\Mvc\ControllerBase;
use PeServer\Core\Store\SessionStore;
use PeServer\Core\Http\ResponsePrinter;
use PeServer\Core\Store\TemporaryStore;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\Core\Mvc\Middleware\IShutdownMiddleware;

/**
 * ルーティング。
 *
 * 名前の割に完全に心臓部だけどWebアプリならこれが心臓でいいのか・・・？ ふとももなのか。
 */
class Routing
{
	/**
	 * ルーティング設定。
	 *
	 * @readonly
	 */
	protected RouteSetting $setting;

	/** @readonly */
	protected CookieStore $cookie;
	/** @readonly */
	protected TemporaryStore $temporary;
	/** @readonly */
	protected SessionStore $session;

	/** @readonly */
	protected ILogger $middlewareLogger;

	/**
	 * 前処理済みミドルウェア一覧。
	 *
	 * @var IMiddleware[]
	 */
	private array $processedMiddleware = [];

	/**
	 * 終了時ミドルウェア。
	 *
	 * 登録の逆順に実行される。
	 *
	 * @var array<IShutdownMiddleware|string>
	 * @phpstan-var array<IShutdownMiddleware|class-string>
	 */
	private array $shutdownMiddleware = [];

	private HttpRequest $shutdownRequest;

	/** @readonly */
	protected HttpMethod $requestMethod;
	/** @readonly */
	protected RequestPath $requestPath;
	/** @readonly */
	protected HttpHeader $requestHeader;

	/**
	 * 生成。
	 *
	 * @param RouteSetting $routeSetting
	 * @param StoreOption $storeOption
	 */
	public function __construct(HttpMethod $requestMethod, RequestPath $requestPath, RouteSetting $routeSetting, StoreOption $storeOption)
	{
		$this->setting = $routeSetting;

		$this->requestMethod = $requestMethod;
		$this->requestPath = $requestPath;
		$this->requestHeader = HttpHeader::getRequest();

		$this->cookie = new CookieStore($storeOption->cookie);
		$this->temporary = new TemporaryStore($storeOption->temporary, $this->cookie);
		$this->session = new SessionStore($storeOption->session, $this->cookie);

		$this->middlewareLogger = Logging::create('middleware');
		$this->shutdownRequest = new HttpRequest($requestMethod, $this->requestHeader, []);
	}

	/**
	 * ミドルウェア取得。
	 *
	 * @param IMiddleware|string $middleware
	 * @phpstan-param IMiddleware|class-string $middleware
	 * @return IMiddleware
	 */
	protected static function getOrCreateMiddleware(IMiddleware|string $middleware): IMiddleware
	{
		if (is_string($middleware)) {
			$instance = new $middleware();
			if (!($instance instanceof IMiddleware)) {
				throw new ArgumentException();
			}
			$middleware = $instance;
		}

		/** @var IMiddleware */
		return $middleware;
	}

	/**
	 * 応答完了ミドルウェア取得。
	 *
	 * @param IShutdownMiddleware|string $middleware
	 * @phpstan-param IShutdownMiddleware|class-string $middleware
	 * @return IShutdownMiddleware
	 */
	protected static function getOrCreateShutdownMiddleware(IShutdownMiddleware|string $middleware): IShutdownMiddleware
	{
		if (is_string($middleware)) {
			$instance = new $middleware();
			if (!($instance instanceof IShutdownMiddleware)) {
				throw new ArgumentException();
			}
			$middleware = $instance;
		}

		/** @var IShutdownMiddleware */
		return $middleware;
	}

	/**
	 * ミドルウェア単独処理。
	 *
	 * @param RequestPath $requestPath
	 * @param HttpRequest $request
	 * @param IMiddleware|string $middleware
	 * @phpstan-param IMiddleware|class-string $middleware
	 * @return bool 次のミドルウェアを実行してよいか
	 */
	private function handleBeforeMiddlewareCore(RequestPath $requestPath, HttpRequest $request, IMiddleware|string $middleware): bool
	{
		$middlewareArgument = new MiddlewareArgument($requestPath, $this->cookie, $this->session, $request, $this->middlewareLogger);
		$middleware = self::getOrCreateMiddleware($middleware);

		$middlewareResult = $middleware->handleBefore($middlewareArgument);

		if ($middlewareResult->canNext()) {
			$this->processedMiddleware[] = $middleware;
			return true;
		}

		$middlewareResult->apply();
		return false;
	}

	/**
	 * ミドルウェアをグワーッと処理。
	 *
	 * @param array<IMiddleware|string> $middleware
	 * @phpstan-param array<IMiddleware|class-string> $middleware
	 * @param HttpRequest $request
	 * @return bool 後続処理は可能か
	 */
	private function handleBeforeMiddleware(array $middleware, HttpRequest $request): bool
	{
		foreach ($middleware as $middlewareItem) {
			$canNext = $this->handleBeforeMiddlewareCore($this->requestPath, $request, $middlewareItem);
			if (!$canNext) {
				return false;
			}
		}

		return true;
	}

	private function handleAfterMiddleware(HttpRequest $request, HttpResponse $response): bool
	{
		if (!ArrayUtility::getCount($this->processedMiddleware)) {
			return true;
		}

		$middlewareArgument = new MiddlewareArgument($this->requestPath, $this->cookie, $this->session, $request, $this->middlewareLogger);
		$middlewareArgument->response = $response;

		$middleware = array_reverse($this->processedMiddleware);
		foreach ($middleware as $middlewareItem) {
			$middlewareResult = $middlewareItem->handleAfter($middlewareArgument);

			if (!$middlewareResult->canNext()) {
				$middlewareResult->apply();
				return false;
			}
		}

		return true;
	}

	private function applyStore(): void
	{
		$this->session->apply();
		$this->temporary->apply();
		$this->cookie->apply();
	}

	/**
	 * アクション実行。
	 *
	 * @param string $rawControllerName
	 * @phpstan-param class-string $rawControllerName
	 * @param ActionSetting $actionSetting
	 * @param string[] $urlParameters
	 * @return void
	 */
	private function executeAction(string $rawControllerName, ActionSetting $actionSetting, array $urlParameters): void
	{
		$splitNames = StringUtility::split($rawControllerName, '/');
		$controllerName = $splitNames[ArrayUtility::getCount($splitNames) - 1];

		$this->shutdownRequest = $request = new HttpRequest($this->requestMethod, $this->requestHeader, $urlParameters);

		// アクション共通ミドルウェア処理
		$this->shutdownMiddleware += $this->setting->actionShutdownMiddleware;
		if (!$this->handleBeforeMiddleware($this->setting->actionMiddleware, $request)) {
			return;
		}

		// アクションに紐づくミドルウェア処理
		$this->shutdownMiddleware += $actionSetting->shutdownMiddleware;
		if (!$this->handleBeforeMiddleware($actionSetting->actionMiddleware, $request)) {
			return;
		}

		$logger = Logging::create($controllerName);
		$controllerArgument = new ControllerArgument($this->cookie, $this->temporary, $this->session, $logger);

		/** @var IActionResult|null */
		$actionResult = null;
		$output = OutputBuffer::get(function () use ($controllerArgument, $controllerName, $actionSetting, $request, &$actionResult) {
			/** @var ControllerBase */
			$controller = new $controllerName($controllerArgument);
			$methodName = $actionSetting->controllerMethod;
			/** @var IActionResult */
			$actionResult = $controller->$methodName($request);
		});
		// 標準出力は闇に葬る
		if ($output->getLength()) {
			$logger->warn('{0}', $output->getRaw());
		}

		$this->applyStore();

		// 最終出力
		/** @var IActionResult $actionResult */
		$response = $actionResult->createResponse();
		if (!$this->handleAfterMiddleware($request, $response)) {
			return;
		}

		$printer = new ResponsePrinter($request, $response);
		$printer->execute();
	}

	/**
	 * メソッド・パスから登録されている処理を実行。
	 *
	 * 失敗時の云々が甘いというかまだなんも考えてない。
	 *
	 * @return void
	 */
	private function executeCore(): void
	{
		$this->shutdownMiddleware += $this->setting->globalShutdownMiddleware;

		// グローバルミドルウェアの適用
		if (ArrayUtility::getCount($this->setting->globalMiddleware)) {
			if (!$this->handleBeforeMiddleware($this->setting->globalMiddleware, $this->shutdownRequest)) {
				return;
			}
		}

		/** @var RouteAction|null */
		$errorAction = null;
		foreach ($this->setting->routes as $route) {
			$action = $route->getAction($this->requestMethod, $this->requestPath);
			if (!is_null($action)) {
				if ($action->status->is(HttpStatus::none())) {
					$this->executeAction($action->className, $action->actionSetting, $action->params);
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
	 * @return void
	 */
	public function execute(): void
	{
		try {
			$this->executeCore();
		} finally {
			$this->shutdown();
		}
	}

	private function shutdown(): void
	{
		if (ArrayUtility::getCount($this->shutdownMiddleware)) {
			$middlewareArgument = new MiddlewareArgument($this->requestPath, $this->cookie, $this->session, $this->shutdownRequest, $this->middlewareLogger);
			$shutdownMiddleware = array_reverse($this->shutdownMiddleware);
			foreach ($shutdownMiddleware as $middleware) {
				$middleware = self::getOrCreateShutdownMiddleware($middleware);
				$middleware->handleShutdown($middlewareArgument);
			}
		}
	}
}
