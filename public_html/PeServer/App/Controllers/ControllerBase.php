<?php declare(strict_types=1);
require_once('PeServer/Libs/smarty/libs/Smarty.class.php');
require_once('PeServer/Core/ControllerArguments.php');

abstract class ControllerBase
{
	public function __construct(ControllerArguments $arguments)
	{ }

	protected function createTemplate(string $baseName): Smarty {
		$smarty = new Smarty();
		$smarty->addTemplateDir("PeServer/App/Views/$baseName/");
		$smarty->addTemplateDir("PeServer/App/Views/");
		$smarty->compile_dir  = "PeServer/temp/views/c/$baseName/";
		$smarty->cache_dir    = "PeServer/temp/views/t/$baseName/";

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
