<?php
require_once('program/lib/smarty/libs/Smarty.class.php');

abstract class ControllerBase
{
	public function view(string $action, ?array $parameters = null) {
		$className = get_class($this);
		$baseName = mb_substr($className, 0, mb_strlen($className) - mb_strlen('Controller'));

		$smarty = new Smarty();
		$smarty->template_dir = "program/app/views/$baseName/";
		$smarty->compile_dir = "program/app/views/$baseName/temp/";

		$smarty->assign($parameters);
		$smarty->display("$action.tpl");
	}
}
