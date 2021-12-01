<?php declare(strict_types=1);
require_once('program/lib/smarty/libs/Smarty.class.php');
require_once('program/core/ControllerArguments.php');

abstract class ControllerBase
{
	public function __construct(ControllerArguments $arguments)
	{ }

	protected function createTemplate(string $baseName): Smarty {
		$smarty = new Smarty();
		$smarty->addTemplateDir("program/app/views/$baseName/");
		$smarty->addTemplateDir("program/app/views/");
		$smarty->compile_dir  = "program/temp/views/c/$baseName/";
		$smarty->cache_dir    = "program/temp/views/t/$baseName/";

		return $smarty;
	}

	public function viewWithController(string $controllerName, string $action, ?array $parameters = null)
	{
		$controllerBaseName = mb_substr($controllerName, 0, mb_strlen($controllerName) - mb_strlen('Controller'));
		$smarty = $this->createTemplate($controllerBaseName);

		$smarty->assign($parameters);
		$smarty->display("$action.tpl");
	}

	public function view(string $action, ?array $parameters = null) {
		$className = get_class($this);

		$this->viewWithController($className, $action, $parameters);
	}
}
