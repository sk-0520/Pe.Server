<?php
require_once('program/lib/smarty/libs/Smarty.class.php');
require_once('program/core/controller-input.php');

abstract class ControllerBase
{
	public function __construct(ControllerInput $input)
	{ }

	protected function createTemplate(string $baseName): Smarty {
		$smarty = new Smarty();
		$smarty->addTemplateDir("program/app/views/$baseName/");
		$smarty->addTemplateDir("program/app/views/");
		$smarty->compile_dir  = "program/temp/views/c/$baseName/";
		$smarty->cache_dir    = "program/temp/views/t/$baseName/";

		return $smarty;
	}

	public function viewWithController(string $controllerBaseName, string $action, ?array $parameters = null)
	{
		$smarty = $this->createTemplate($controllerBaseName);

		$smarty->assign($parameters);
		$smarty->display("$action.tpl");
	}

	public function view(string $action, ?array $parameters = null) {
		$className = get_class($this);
		$controllerBaseName = mb_substr($className, 0, mb_strlen($className) - mb_strlen('Controller'));

		$this->viewWithController($controllerBaseName, $action, $parameters);
	}
}
