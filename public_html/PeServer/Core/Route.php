<?php

declare(strict_types=1);

namespace PeServer\Core;

require_once('PeServer/Core/Action.php');
require_once('PeServer/Core/ActionRequest.php');
require_once('PeServer/Core/HttpMethod.php');

class Route
{
	private $path;
	private $className;
	private $actions = array();

	public function __construct(string $path, string $className)
	{
		// if(str_starts_with($path, '/')) {
		// 	die();
		// }
		// if(str_ends_with($path, '/')) {
		// 	die();
		// }
		if (mb_substr($path, 0, 1) == '/') {
			die();
		}

		$this->path = $path;
		$this->className = $className;
		if (mb_substr($this->path, 0, 3) != 'api') {
			$this->actions[''] = new Action(HttpMethod::ALL, 'index');
		}
	}

	public function action(string $httpMethod, string $actionName, ?string $methodName = null): Route
	{
		$actions[$actionName] = new Action(
			$httpMethod,
			$methodName != null ? $methodName : $actionName
		);
		return $this;
	}

	public function getAction(string $httpMethod, array $paths)
	{
		$path = implode('/', $paths);

		if ($this->path != mb_substr($path, 0, mb_strlen($this->path))) {
			return null;
		}

		$actionPath = mb_substr($path, mb_strlen($this->path));

		if (stripos($actionPath, '/') !== false) {
			return null;
		}

		if (!isset($this->actions[$actionPath])) {
			return null;
		}

		$action = $this->actions[$actionPath];
		//TODO: メソッド判定

		return [
			'class' => $this->className,
			'method' => $action->callMethod,
		];
	}
}
