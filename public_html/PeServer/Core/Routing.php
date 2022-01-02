<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Exception;
use \PeServer\Core\Log\Logging;
use \PeServer\Core\FilterArgument;
use \PeServer\Core\Mvc\ActionRequest;
use \PeServer\Core\Mvc\IActionResult;
use \PeServer\Core\Store\CookieStore;
use \PeServer\Core\Mvc\ControllerBase;
use \PeServer\Core\Store\CookieOption;
use \PeServer\Core\Store\TemporaryOption;
use \PeServer\Core\Store\SessionOption;
use \PeServer\Core\Store\SessionStore;
use \PeServer\App\Models\SessionManager;
use PeServer\Core\Mvc\ActionResult;
use \PeServer\Core\Mvc\ControllerArgument;
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
	private array $filters;
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
	 * @param array{global_filters:IActionFilter[],routes:Route[]} $routeSetting
	 * @param array{cookie:CookieOption,temporary:TemporaryOption,session:SessionOption} $storeOption
	 */
	public function __construct(array $routeSetting, array $storeOption)
	{
		$this->filters = $routeSetting['global_filters'];
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
	 * @return void
	 */
	private function filter(array $requestPaths, ActionRequest $request, IActionFilter $filter): void
	{
		$filterArgument = new FilterArgument($requestPaths, $this->cookie, $this->session, $request, $this->filterLogger);
		$filterResult = $filter->filtering($filterArgument);

		if (400 <= $filterResult->status->code()) {
			throw new Exception('TODO: ' . $filterResult->status->code());
		}
	}

	/**
	 * アクション実行。
	 *
	 * @param string[] $requestPaths
	 * @param string $rawControllerName
	 * @param string $methodName
	 * @param string[] $urlParameters
	 * @param ActionOption[] $options
	 * @return no-return
	 */
	private function executeAction(array $requestPaths, string $rawControllerName, string $methodName, array $urlParameters, array $options): void
	{
		$splitNames = explode('/', $rawControllerName);
		$controllerName = $splitNames[count($splitNames) - 1];

		$request = new ActionRequest($urlParameters);

		foreach ($options as $option) {
			if (!is_null($option->filter)) {
				$this->filter($requestPaths, $request, $option->filter);
			}
		}

		$logger = Logging::create($controllerName);
		$controllerArgument = new ControllerArgument($this->cookie, $this->temporary, $this->session, $logger);

		/** @var ControllerBase */
		$controller = new $controllerName($controllerArgument);
		/** @var IActionResult */
		$actionResult = $controller->$methodName($request);
		$controller->execute($actionResult);
		exit;
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
		$requestPaths = $this->getPathValues($requestUri);

		if (ArrayUtility::getCount($this->filters)) {
			$request = new ActionRequest([]);
			foreach ($this->filters as $filter) {
				$this->filter($requestPaths, $request, $filter);
			}
		}

		foreach ($this->routeMap as $route) {
			$action = $route->getAction($requestMethod, $requestPaths);
			if (!is_null($action)) {
				if ($action['code']->code() === HttpStatus::doExecute()->code()) {
					$this->executeAction($requestPaths, $action['class'], $action['method'], $action['params'], $action['options']);
					exit; //@phpstan-ignore-line executeActionで終わるけどここだけ見たら分からないので。
				}
			}
		}
	}
}
