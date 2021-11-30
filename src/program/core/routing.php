<?php
require_once('program/core/route.php');

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
		var_dump($reqs);

		$rawPaths = explode('/', $reqs[0]);
		$paths = array_slice($rawPaths, 1);

		return $paths;
	}

	private function executeAction($controllerName, $methodName) {
		$file = $this->controllerBaseDirectory . '/' . $controllerName . '.php';
		require_once($file);

		$ci = new ControllerInput();

		$controller = new $controllerName($ci);
		$controller->$methodName();
	}

	public function execute(string $requestUri)
	{
		$paths = $this->splitPaths($requestUri);

		foreach($this->routeMap as $route) {
			$action = $route->getAction($paths);
			if($action) {
				$this->executeAction($action['class'], $action['method']);
			}
		}

		var_dump($paths);
		echo $requestUri;
	}
}

