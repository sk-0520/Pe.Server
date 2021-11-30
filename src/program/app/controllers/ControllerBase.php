<?php
require_once('program/lib/smarty/libs/Smarty.class.php');
require_once('program/core/controller-input.php');

abstract class ControllerBase
{
	public function __construct(ControllerInput $input)
	{ }

	public function view(string $action, ?array $parameters = null) {
		$className = get_class($this);
		$baseName = mb_substr($className, 0, mb_strlen($className) - mb_strlen('Controller'));

		$smarty = new Smarty();
		$smarty->addTemplateDir("program/app/views/$baseName/");
		$smarty->addTemplateDir("program/app/views/");
		$smarty->compile_dir  = "program/temp/views/c/$baseName/";
		$smarty->cache_dir    = "program/temp/views/t/$baseName/";

		$smarty->assign($parameters);
		$smarty->display("$action.tpl");
	}
}
