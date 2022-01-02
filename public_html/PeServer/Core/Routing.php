<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Exception;
use PeServer\Core\Log\Logging;
use PeServer\Core\FilterArgument;
use PeServer\Core\Mvc\ActionRequest;
use PeServer\Core\Mvc\IActionResult;
use PeServer\Core\Store\CookieStore;
use PeServer\Core\Mvc\ControllerBase;
use PeServer\Core\Store\CookieOption;
use PeServer\Core\Store\TemporaryOption;
use PeServer\Core\Store\SessionOption;
use PeServer\Core\Store\SessionStore;
use PeServer\App\Models\SessionManager;
use PeServer\Core\Mvc\ActionResult;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\Core\Store\TemporaryStore;

/**
 * ルーティング。
 */
class Routing
{
	/**
	 * グローバルフィルタ処理
	 *
	 * @var IActionFilter[]
	 */
	private array $globalFilters;
	/**
	 * アクション共通フィルタ処理
	 *
	 * @var IActionFilter[]
	 */
	private array $actionFilters;
	/**
	 * ルーティング情報。
	 *
	 * @var Route[]
	 */
	private $routeMap;

	private CookieStore $cookie;
	private TemporaryStore $temporary;
	private SessionStore $session;

	private ILogger $filterLogger;

	/**
	 * 生成。
	 *
	 * @param array{global_filters:IActionFilter[],action_filters:IActionFilter[],routes:Route[]} $routeSetting
	 * @param array{cookie:CookieOption,temporary:TemporaryOption,session:SessionOption} $storeOption
	 */
	public function __construct(array $routeSetting, array $storeOption)
	{
		$this->globalFilters = $routeSetting['global_filters'];
		$this->actionFilters = $routeSetting['action_filters'];
		$this->routeMap = $routeSetting['routes'];

		$this->cookie = new CookieStore($storeOption['cookie']);
		$this->temporary = new TemporaryStore($storeOption['temporary'], $this->cookie);
		$this->session = new SessionStore($storeOption['session'], $this->cookie);

		SessionManager::initialize($this->session);

		$this->filterLogger = Logging::create('filtering');
	}

	/**
	 * パス部分を取得。
	 *
	 * @param string $requestUri
	 * @return string[] クエリを含まないパス一覧。
	 */
	private function getPathValues(string $requestUri): array
	{
		$reqs = explode('?', $requestUri, 2);

		$paths = explode('/', trim($reqs[0], '/'));

		return $paths;
	}

	/**
	 * Undocumented function
	 *
	 * @param string[] $requestPaths
	 * @param ActionRequest $request
	 * @param IActionFilter $filter
	 * @return bool 次のフィルタを実行してよいか
	 */
	private function filter(array $requestPaths, ActionRequest $request, IActionFilter $filter): bool
	{
		$filterArgument = new FilterArgument($requestPaths, $this->cookie, $this->session, $request, $this->filterLogger);
		$filterResult = $filter->filtering($filterArgument);

		if ($filterResult->canNext()) {
			return true;
		}

		$filterResult->apply();
		return false;
	}

	/**
	 * アクション実行。
	 *
	 * @param string[] $requestPaths
	 * @param string $rawControllerName
	 * @param string $methodName
	 * @param string[] $urlParameters
	 * @param ActionOption[] $options
	 * @return void
	 */
	private function executeAction(array $requestPaths, string $rawControllerName, string $methodName, array $urlParameters, array $options): void
	{
		$splitNames = explode('/', $rawControllerName);
		$controllerName = $splitNames[count($splitNames) - 1];

		$request = new ActionRequest($urlParameters);

		// アクション共通フィルタ処理
		foreach ($this->actionFilters as $filter) {
			$canNext = $this->filter($requestPaths, $request, $filter);
			if (!$canNext) {
				return;
			}
		}

		// アクションに紐づくフィルタ処理
		foreach ($options as $option) {
			if (!is_null($option->filter)) {
				$canNext = $this->filter($requestPaths, $request, $option->filter);
				if (!$canNext) {
					return;
				}
			}
		}

		$logger = Logging::create($controllerName);
		$controllerArgument = new ControllerArgument($this->cookie, $this->temporary, $this->session, $logger);

		/** @var ControllerBase */
		$controller = new $controllerName($controllerArgument);
		/** @var IActionResult */
		$actionResult = $controller->$methodName($request);
		$controller->execute($actionResult);
	}

	/**
	 * メソッド・パスから登録されている処理を実行。
	 *
	 * 失敗時の云々が甘いというかまだなんも考えてない。
	 *
	 * @param string $requestMethod HttpMethod を参照。
	 * @param string $requestUri リクエストURL。
	 * @return void
	 */
	private function executeCore(string $requestMethod, string $requestUri): void
	{
		$requestPaths = $this->getPathValues($requestUri);

		// グローバルフィルタの適用
		if (ArrayUtility::getCount($this->globalFilters)) {
			$request = new ActionRequest([]);
			foreach ($this->globalFilters as $filter) {
				$canNext = $this->filter($requestPaths, $request, $filter);
				if (!$canNext) {
					return;
				}
			}
		}

		/** @var array{code:HttpStatus,class:string,method:string,params:array<string,string>,options:ActionOption[]}|null */
		$errorAction = null;
		foreach ($this->routeMap as $route) {
			$action = $route->getAction($requestMethod, $requestPaths);
			if (!is_null($action)) {
				if ($action['code']->code() === HttpStatus::none()->code()) {
					$this->executeAction($requestPaths, $action['class'], $action['method'], $action['params'], $action['options']);
					return;
				} else if (is_null($errorAction)) {
					$errorAction = $action;
				}
			}
		}

		if (is_null($errorAction)) {
			FilterResult::error(HttpStatus::internalServerError())->apply();
		} else {
			FilterResult::error($errorAction['code'])->apply();
		}
	}

	/**
	 * メソッド・パスから登録されている処理を実行。
	 *
	 * 失敗時の云々が甘いというかまだなんも考えてない。
	 *
	 * @param string $requestMethod HttpMethod を参照。
	 * @param string $requestUri リクエストURL。
	 * @return void
	 */
	public function execute(string $requestMethod, string $requestUri): void
	{
		$this->executeCore($requestMethod, $requestUri);
	}
}
