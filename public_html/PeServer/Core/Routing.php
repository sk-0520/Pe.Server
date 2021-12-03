<?php

declare(strict_types=1);

namespace PeServer\Core;

require_once('PeServer/Core/ControllerArguments.php');
require_once('PeServer/Core/ActionRequest.php');
require_once('PeServer/Core/Route.php');

class Routing
{
	private $routeMap;
	private $controllerBaseDirectory;

	public function __construct(array $routeMap, string $controllerBaseDirectory)
	{
		$this->routeMap = $routeMap;
		$this->controllerBaseDirectory = $controllerBaseDirectory;
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

		$controllerArguments = new ControllerArguments();
		$req = new ActionRequest();

		$controller = new $controllerName($controllerArguments);
		$controller->$methodName($req);
	}

	public function execute(string $requestMethod, string $requestUri)
	{
		$paths = $this->splitPaths($requestUri);
		$requestPaths = $paths;
		//TODO: パス中のパラメータ(/区切りのID的な)
		$pathParameters = $paths;

		foreach ($this->routeMap as $route) {
			$action = $route->getAction($requestMethod, $requestPaths);
			if ($action) {
				$this->executeAction($action['class'], $action['method'], $pathParameters);
			}
		}
	}
}
