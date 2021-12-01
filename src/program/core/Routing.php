<?php declare(strict_types=1);
require_once('program/core/ControllerArguments.php');
require_once('program/core/ActionRequest.php');
require_once('program/core/Route.php');

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

	private function executeAction($rawControllerName, $methodName) {
		$file = $this->controllerBaseDirectory . '/' . $rawControllerName . '.php';
		require_once($file);


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

		foreach($this->routeMap as $route) {
			$action = $route->getAction($requestMethod, $paths);
			if($action) {
				$this->executeAction($action['class'], $action['method']);
			}
		}

		var_dump($paths);
		echo $requestUri;
	}
}
