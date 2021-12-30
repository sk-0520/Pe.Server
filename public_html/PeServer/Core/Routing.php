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
use \PeServer\Core\Store\SessionStore;
use \PeServer\Core\Store\SessionOption;
use \PeServer\App\Models\SessionManager;
use \PeServer\Core\Mvc\ControllerArgument;

/**
 * ルーティング。
 */
class Routing
{
	/**
	 * ルーティング情報。
	 *
	 * @var Route[]
	 */
	private $routeMap;

	private CookieStore $cookie;
	private SessionStore $session;

	/**
	 * 生成。
	 *
	 * @param Route[] $routeMap ルーティング情報
	 * @param array{cookie:CookieOption,session:SessionOption} $storeOption
	 */
	public function __construct(array $routeMap, array $storeOption)
	{
		$this->routeMap = $routeMap;

		$this->cookie = new CookieStore($storeOption['cookie']);
		$this->session = new SessionStore($storeOption['session'], $this->cookie);

		SessionManager::initialize($this->session);
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
	 * アクション実行。
	 *
	 * @param string $rawControllerName
	 * @param string $methodName
	 * @param string[] $urlParameters
	 * @param ActionOption[] $options
	 * @return no-return
	 */
	private function executeAction(string $rawControllerName, string $methodName, array $urlParameters, array $options): void
	{
		$splitNames = explode('/', $rawControllerName);
		$controllerName = $splitNames[count($splitNames) - 1];

		foreach ($options as $option) {
			if (!is_null($option->filter)) {
				$filterLogger = Logging::create('filter');
				$filter = $option->filter;
				$filterArgument = new FilterArgument($this->cookie, $this->session, $filterLogger);
				$filterResult = $filter($filterArgument);
				$httpStatus = is_array($filterResult) ? $filterResult['status']: $filterResult;
				if (400 <= $httpStatus->code()) {
					throw new Exception('TODO: ' . $httpStatus->code());
				}
			}
		}

		$logger = Logging::create($controllerName);
		$controllerArgument = new ControllerArgument($this->cookie, $this->session, $logger);
		$request = new ActionRequest($urlParameters);

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
		$paths = $this->getPathValues($requestUri);
		$requestPaths = $paths;

		foreach ($this->routeMap as $route) {
			$action = $route->getAction($requestMethod, $requestPaths);
			if (!is_null($action)) {
				if ($action['code']->code() === HttpStatus::doExecute()->code()) {
					$this->executeAction($action['class'], $action['method'], $action['params'], $action['options']);
					exit; //@phpstan-ignore-line executeActionで終わるけどここだけ見たら分からないので。
				}
			}
		}
	}
}
