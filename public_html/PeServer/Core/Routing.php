<?php

declare(strict_types=1);

namespace PeServer\Core;

use \PeServer\Core\Log\Logging;
use \PeServer\Core\Mvc\ControllerArguments;

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
	private $_routeMap;

	/**
	 * 生成。
	 *
	 * @param Route[] $routeMap ルーティング情報
	 */
	public function __construct(array $routeMap)
	{
		$this->_routeMap = $routeMap;
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
	private function executeAction(string $rawControllerName, string $methodName, array $urlParameters, ActionOptions $options): void
	{
		$splitNames = explode('/', $rawControllerName);
		$controllerName = $splitNames[count($splitNames) - 1];

		$logger = Logging::create($controllerName);

		$controllerArguments = new ControllerArguments($logger);
		$request = new ActionRequest($urlParameters);

		$controller = new $controllerName($controllerArguments);
		$controller->$methodName($request, $options);
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

		foreach ($this->_routeMap as $route) {
			$action = $route->getAction($requestMethod, $requestPaths);
			if ($action['code'] === HttpStatusCode::DO_EXECUTE) {
				$this->executeAction($action['class'], $action['method'], $action['params'], $action['options']);
			}
		}
	}
}
