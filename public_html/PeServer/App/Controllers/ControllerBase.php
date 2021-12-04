<?php

declare(strict_types=1);

namespace PeServer\App\Controllers;

require_once('PeServer/Libs/smarty/libs/Smarty.class.php');

use \Smarty;
use \PeServer\Core\ControllerArguments;
use \PeServer\Core\ILogger;
use \PeServer\Core\Logging;
use \PeServer\App\Models\Template;

abstract class ControllerBase
{
	protected $logger;

	public function __construct(ControllerArguments $arguments)
	{
		$this->logger = Logging::create(self::class);
		$this->logger->trace('create');
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
