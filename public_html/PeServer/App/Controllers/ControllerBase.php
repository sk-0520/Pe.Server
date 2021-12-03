<?php declare(strict_types=1);
namespace PeServer\App\Controllers;

use \Smarty;
use \PeServer\Core\ControllerArguments;

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
		$lastWord = 'Controller';
		$skipBaseName = 'PeServer\\App\\Controllers';
		$controllerClassName = mb_substr($controllerName, mb_strpos($controllerName, $skipBaseName) + mb_strlen($skipBaseName) + 1);
		$controllerBaseName = mb_substr($controllerClassName, 0, mb_strlen($controllerClassName) - mb_strlen($lastWord));

		$templateDirPath = str_replace('\\', DIRECTORY_SEPARATOR, $controllerBaseName);
		$smarty = $this->createTemplate($templateDirPath);

		$smarty->assign($parameters);
		$smarty->display("$action.tpl");
	}

	public function view(string $action, ?array $parameters = null) {
		$className = get_class($this);

		$this->viewWithController($className, $action, $parameters);
	}
}
