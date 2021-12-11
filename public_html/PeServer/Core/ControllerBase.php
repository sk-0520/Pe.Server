<?php

declare(strict_types=1);

namespace PeServer\Core;

use \LogicException;
use \Smarty;
use \PeServer\Core\ActionRequest;
use \PeServer\Core\ControllerArguments;
use \PeServer\Core\Template;
use \PeServer\Core\LogicBase;
use \PeServer\Core\LogicParameter;
use \PeServer\Core\Log\Logging;

abstract class ControllerBase
{
	protected $logger;
	protected $skipBaseName = 'PeServer\\App\\Controllers';

	public function __construct(ControllerArguments $arguments)
	{
		$this->logger = $arguments->logger;

		$this->logger->trace('CONTROLLER');
	}

	protected function createParameter(string $logicName, ActionRequest $request): LogicParameter
	{
		return new LogicParameter(
			$request,
			Logging::create($logicName)
		);
	}

	protected function createLogic($logicClass, ActionRequest $request): LogicBase
	{
		$parameter = $this->createParameter($logicClass, $request);
		return new $logicClass($parameter);
	}

	protected function createTemplate(string $baseName): Smarty
	{
		return Template::createTemplate($baseName);
	}

	public function viewWithController(string $controllerName, string $action, int $httpStatusCode, ?array $parameters = null)
	{
		$lastWord = 'Controller';
		$controllerClassName = mb_substr($controllerName, mb_strpos($controllerName, $this->skipBaseName) + mb_strlen($this->skipBaseName) + 1);
		$controllerBaseName = mb_substr($controllerClassName, 0, mb_strlen($controllerClassName) - mb_strlen($lastWord));

		$templateDirPath = str_replace('\\', DIRECTORY_SEPARATOR, $controllerBaseName);
		$smarty = $this->createTemplate($templateDirPath);

		$smarty->assign($parameters);
		$smarty->display("$action.tpl");
	}

	public function view(string $action, ?array $parameters = null)
	{
		$className = get_class($this);

		$httpStatusCode = ArrayUtility::getOr($parameters, 'status', HttpStatusCode::OK);

		$this->viewWithController($className, $action, $httpStatusCode, $parameters);
	}


	public function data(ActionResponse $response)
	{
		header('Content-Type: ' . $response->mime);
		if ($response->chunked) {
			header("Transfer-encoding: chunked");
		}

		if(is_null($response->callback)) {
			$converter = new ResponseOutput();
			$converter->output($response->mime, $response->chunked, $response->data);
		} else {
			call_user_func_array($this->callback, [$response->mime, $response->chunked, $response->data]);
		}
	}
}
