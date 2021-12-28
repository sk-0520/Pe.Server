<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Exception;
use \PeServer\Core\Log\Logging;
use \PeServer\Core\Mvc\ControllerArgument;
use \PeServer\Core\Store\SessionStore;
use \PeServer\Core\FilterArgument;
use \PeServer\Core\Store\CookieOption;
use \PeServer\Core\Store\CookieStore;

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
	 */
	public function __construct(array $routeMap, CookieOption $cookieOption)
	{
		$this->routeMap = $routeMap;

		$this->cookie = new CookieStore($cookieOption);
		$this->session = new SessionStore($this->cookie);
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
	 * @return void
	 */
	private function executeAction(string $rawControllerName, string $methodName, array $urlParameters, ActionOption $option): void
	{
		$splitNames = explode('/', $rawControllerName);
		$controllerName = $splitNames[count($splitNames) - 1];

		if (!is_null($option->filter)) {
			$filter = $option->filter;
			$filterArgument = new FilterArgument($this->cookie, $this->session);
			$httpStatus = $filter($filterArgument);
			if (400 <= $httpStatus->code()) {
				throw new Exception('TODO: ' . $httpStatus->code());
			}
		}

		$logger = Logging::create($controllerName);
		$controllerArgument = new ControllerArgument($this->cookie, $this->session, $logger);
		$request = new ActionRequest($urlParameters);

		$controller = new $controllerName($controllerArgument);
		$controller->$methodName($request);
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
				}
			}
		}
	}
}
