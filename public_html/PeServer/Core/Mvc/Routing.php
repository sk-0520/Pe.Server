<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\Collections\Arr;
use PeServer\Core\DI\IDiRegisterContainer;
use PeServer\Core\Http\HttpHeader;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\Http\ResponsePrinter;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Log\Logging;
use PeServer\Core\Mvc\ActionSetting;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\Core\Mvc\ControllerBase;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\IShutdownMiddleware;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;
use PeServer\Core\Mvc\Result\IActionResult;
use PeServer\Core\Mvc\RouteAction;
use PeServer\Core\Mvc\RouteSetting;
use PeServer\Core\OutputBuffer;
use PeServer\Core\ReflectionUtility;
use PeServer\Core\Store\Stores;
use PeServer\Core\Text;

/**
 * ルーティング。
 *
 * 名前の割に完全に心臓部だけどWebアプリならこれが心臓でいいのか・・・？ ふとももなのか。
 */
class Routing
{
	#region variable

	/**
	 * ルーティング設定。
	 *
	 * @readonly
	 */
	protected RouteSetting $setting;

	/** @readonly */
	protected Stores $stores;

	/** @readonly */
	protected ILoggerFactory $loggerFactory;

	/**
	 * このルーティング内で使いまわされるDI。
	 *
	 * @readonly
	 */
	protected IDiRegisterContainer $serviceLocator;

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
	 * @phpstan-var array<IShutdownMiddleware|class-string<IShutdownMiddleware>>
	 */
	private array $shutdownMiddleware = [];

	/**
	 * 終了処理時に使用する要求データ。
	 *
	 * 要求受付前はダミー値が入っており、要求受付後はその要求値が格納される。
	 *
	 * @var HttpRequest
	 */
	private HttpRequest $shutdownRequest;

	/** @readonly */
	protected HttpMethod $requestMethod;
	/** @readonly */
	protected RequestPath $requestPath;
	/** @readonly */
	protected HttpHeader $requestHeader;

	#endregion

	/**
	 * 生成。
	 *
	 * @param RouteRequest $routeRequest
	 * @param RouteSetting $routeSetting
	 * @param Stores $stores
	 */
	public function __construct(RouteRequest $routeRequest, RouteSetting $routeSetting, Stores $stores, ILoggerFactory $loggerFactory, IDiRegisterContainer $serviceLocator)
	{
		$this->requestMethod = $routeRequest->method;
		$this->requestPath = $routeRequest->path;
		$this->setting = $routeSetting;
		$this->stores = $stores;
		$this->loggerFactory = $loggerFactory;
		$this->serviceLocator = $serviceLocator;

		$this->requestHeader = HttpHeader::getRequest();
		$this->shutdownRequest = new HttpRequest($this->stores->special, $this->requestMethod, $this->requestHeader, []);
		$this->serviceLocator->registerValue($this->shutdownRequest);
	}

	#region function

	/**
	 * ミドルウェア取得。
	 *
	 * @param IMiddleware|string $middleware
	 * @phpstan-param IMiddleware|class-string<IMiddleware> $middleware
	 * @return IMiddleware
	 */
	protected function getOrCreateMiddleware(IMiddleware|string $middleware): IMiddleware
	{
		if (is_string($middleware)) {
			/** @var IMiddleware */
			//$middleware = ReflectionUtility::create($middleware, IMiddleware::class);
			$middleware = $this->serviceLocator->new($middleware);
		}

		return $middleware;
	}

	/**
	 * 応答完了ミドルウェア取得。
	 *
	 * @param IShutdownMiddleware|string $middleware
	 * @phpstan-param IShutdownMiddleware|class-string<IShutdownMiddleware> $middleware
	 * @return IShutdownMiddleware
	 */
	protected function getOrCreateShutdownMiddleware(IShutdownMiddleware|string $middleware): IShutdownMiddleware
	{
		if (is_string($middleware)) {
			/** @var IShutdownMiddleware */
			//$middleware = ReflectionUtility::create($middleware, IShutdownMiddleware::class);
			$middleware = $this->serviceLocator->new($middleware);
		}

		return $middleware;
	}

	/**
	 * ミドルウェア単独処理。
	 *
	 * @param RequestPath $requestPath
	 * @param HttpRequest $request
	 * @param IMiddleware|string $middleware
	 * @phpstan-param IMiddleware|class-string<IMiddleware> $middleware
	 * @return bool 次のミドルウェアを実行してよいか
	 */
	private function handleBeforeMiddlewareCore(RequestPath $requestPath, HttpRequest $request, IMiddleware|string $middleware): bool
	{
		$middlewareArgument = new MiddlewareArgument($requestPath, $this->stores, $request);
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
	 * ミドルウェア(事前)をグワーッと処理。
	 *
	 * @param array<IMiddleware|string> $middleware
	 * @phpstan-param array<IMiddleware|class-string<IMiddleware>> $middleware
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

	/**
	 * ミドルウェア(事後)をグワーッと処理。
	 *
	 * @param HttpRequest $request
	 * @param HttpResponse $response
	 * @return bool
	 */
	private function handleAfterMiddleware(HttpRequest $request, HttpResponse $response): bool
	{
		if (!Arr::getCount($this->processedMiddleware)) {
			return true;
		}

		$middlewareArgument = new MiddlewareArgument($this->requestPath, $this->stores, $request);

		$middleware = Arr::reverse($this->processedMiddleware);
		foreach ($middleware as $middlewareItem) {
			$middlewareResult = $middlewareItem->handleAfter($middlewareArgument, $response);

			if (!$middlewareResult->canNext()) {
				$middlewareResult->apply();
				return false;
			}
		}

		return true;
	}

	/**
	 * アクション実行。
	 *
	 * @param string $rawControllerName
	 * @phpstan-param class-string<ControllerBase> $rawControllerName
	 * @param ActionSetting $actionSetting
	 * @param array<string,string> $urlParameters
	 * @phpstan-param array<non-empty-string,string> $urlParameters
	 * @return void
	 */
	private function executeAction(string $rawControllerName, ActionSetting $actionSetting, array $urlParameters): void
	{
		$splitNames = Text::split($rawControllerName, '/');
		/** @phpstan-var class-string<ControllerBase> */
		$controllerName = $splitNames[Arr::getCount($splitNames) - 1];

		// HTTPリクエストデータをDI再登録
		$request = new HttpRequest($this->stores->special, $this->requestMethod, $this->requestHeader, $urlParameters);
		$this->serviceLocator->registerValue($request);
		$this->shutdownRequest = $request;

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

		$logger = $this->loggerFactory->createLogger($controllerName);
		$controllerArgument = $this->serviceLocator->new(ControllerArgument::class, [Stores::class => $this->stores, ILogger::class => $logger]);

		/** @var IActionResult|null */
		$actionResult = null;
		$output = OutputBuffer::get(function () use ($controllerArgument, $controllerName, $actionSetting, &$actionResult) {
			/** @var ControllerBase */
			$controller = $this->serviceLocator->new($controllerName, [ControllerArgument::class => $controllerArgument]);
			$methodName = $actionSetting->controllerMethod;
			/** @var IActionResult */
			$actionResult = $this->serviceLocator->call([$controller, $methodName]); //@phpstan-ignore-line callable
		});
		// 標準出力は闇に葬る
		if ($output->count()) {
			$logger->warn('{0}', $output->getRaw());
		}

		$this->stores->apply();

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
	 */
	private function executeCore(): void
	{
		$this->shutdownMiddleware += $this->setting->globalShutdownMiddleware;

		// グローバルミドルウェアの適用
		if (Arr::getCount($this->setting->globalMiddleware)) {
			if (!$this->handleBeforeMiddleware($this->setting->globalMiddleware, $this->shutdownRequest)) {
				return;
			}
		}

		/** @var RouteAction|null */
		$errorAction = null;
		foreach ($this->setting->routes as $route) {
			$action = $route->getAction($this->requestMethod, $this->requestPath);
			if ($action !== null) {
				if ($action->status->is(HttpStatus::none())) {
					$this->executeAction($action->className, $action->actionSetting, $action->params);
					return;
				} else if ($errorAction === null) {
					$errorAction = $action;
				}
			}
		}

		if ($errorAction === null) {
			MiddlewareResult::error(HttpStatus::internalServerError())->apply();
		} else {
			MiddlewareResult::error($errorAction->status)->apply();
		}
	}

	/**
	 * メソッド・パスから登録されている処理を実行。
	 */
	public function execute(): void
	{
		try {
			$this->executeCore();
		} finally {
			$this->shutdown();
		}
	}

	/**
	 * 終了処理。
	 */
	private function shutdown(): void
	{
		if (Arr::getCount($this->shutdownMiddleware)) {
			$middlewareArgument = new MiddlewareArgument($this->requestPath, $this->stores, $this->shutdownRequest);
			$shutdownMiddleware = array_reverse($this->shutdownMiddleware);
			foreach ($shutdownMiddleware as $middleware) {
				$middleware = self::getOrCreateShutdownMiddleware($middleware);
				$middleware->handleShutdown($middlewareArgument);
			}
		}
	}

	#endregion
}
