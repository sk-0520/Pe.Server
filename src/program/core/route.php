<?php

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
		if(mb_substr($path, 0, 1 ) == '/') {
			die();
		}

		$this->path = $path;
		$this->className = $className;
		$this->actions[''] = 'index';
	}

	public function action(string $actionName, ?string $methodName = null): Route
	{
		$actions[$actionName] = $methodName != null ? $methodName: $actionName;
		return $this;
	}

	public function getAction(array $paths) {
		$path = implode('/', $paths);

		if($this->path != mb_substr($path, 0, mb_strlen($this->path))) {
			return null;
		}

		$action = mb_substr($path, mb_strlen($this->path));

		if(stripos($action, '/') !== false) {
			return null;
		}

		if(!isset($this->actions[$action])) {
			return null;
		}

		return [
			'class' => $this->className,
			'method' => $this->actions[$action],
		];
	}
}


