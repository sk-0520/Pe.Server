<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Log\Logging;


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

	/**
	 * 生成。
	 *
	 * @param Route[] $routeMap ルーティング情報
	 */
	public function __construct(array $routeMap)
	{
		$this->routeMap = $routeMap;
	}

	/**
	 * パス分割。
	 *
	 * @param string $requestUri
	 * @return string[]
	 */
	private function splitPaths(string $requestUri): array
	{
		$reqs = explode('?', $requestUri, 2);

		$rawPaths = explode('/', $reqs[0]);
		$paths = array_slice($rawPaths, 1);

		return $paths;
	}

	/**
	 * アクション実行。
	 *
	 * @param string $rawControllerName
	 * @param string $methodName
	 * @param string[] $pathParameters
	 * @return void
	 */
	private function executeAction(string $rawControllerName, string $methodName, array $pathParameters): void
	{
		$splitNames = explode('/', $rawControllerName);
		$controllerName = $splitNames[count($splitNames) - 1];

		$logger = Logging::create($controllerName);

		$controllerArguments = new ControllerArguments($logger);
		$request = new ActionRequest($pathParameters);

		$controller = new $controllerName($controllerArguments);
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
		$paths = $this->splitPaths($requestUri);
		$requestPaths = $paths;
		//TODO: パス中のパラメータ(/区切りのID的な)
		$pathParameters = array();

		foreach ($this->routeMap as $route) {
			$action = $route->getAction($requestMethod, $requestPaths);
			if ($action) {
				$this->executeAction($action['class'], $action['method'], $pathParameters);
			}
		}
	}
}
