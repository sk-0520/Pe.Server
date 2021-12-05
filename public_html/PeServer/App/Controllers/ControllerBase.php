<?php

declare(strict_types=1);

namespace PeServer\App\Controllers;

require_once('PeServer/Libs/smarty/libs/Smarty.class.php');

use \Smarty;
use \PeServer\Core\ControllerArguments;
use \PeServer\Core\Log\Logging;
use \PeServer\App\Models\Template;
use \PeServer\App\Models\Domains\LogicBase;
use \PeServer\App\Models\Domains\LogicParameter;

abstract class ControllerBase
{
	protected $logger;

	public function __construct(ControllerArguments $arguments)
	{
		$this->logger = $arguments->logger;

		$this->logger->trace('CONTROLLER');
	}

	protected function createParameter(string $logicName): LogicParameter
	{
		return new LogicParameter(
			Logging::create($logicName)
		);
	}

	protected function createLogic($logicClass): LogicBase
	{
		$parameter = $this->createParameter($logicClass);
		return new $logicClass($parameter);
	}

	protected function createTemplate(string $baseName): Smarty
	{
		return Template::createTemplate($baseName);
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

	public function view(string $action, ?array $parameters = null)
	{
		$className = get_class($this);

		$this->viewWithController($className, $action, $parameters);
	}
}
