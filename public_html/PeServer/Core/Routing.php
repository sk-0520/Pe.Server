<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Log\Logging;


class Routing
{
	private $routeMap;

	public function __construct(array $routeMap)
	{
		$this->routeMap = $routeMap;
	}

	private function splitPaths(string $requestUri)
	{
		$reqs = explode('?', $requestUri, 2);

		$rawPaths = explode('/', $reqs[0]);
		$paths = array_slice($rawPaths, 1);

		return $paths;
	}

	private function executeAction($rawControllerName, $methodName, array $pathParameters)
	{
		$splitNames = explode('/', $rawControllerName);
		$controllerName = $splitNames[count($splitNames) - 1];

		$logger = Logging::create($controllerName);

		$controllerArguments = new ControllerArguments($logger);
		$request = new ActionRequest($pathParameters);

		$controller = new $controllerName($controllerArguments);
		$controller->$methodName($request);
	}

	public function execute(string $requestMethod, string $requestUri)
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
