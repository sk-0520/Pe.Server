<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplatePlugin;

use \Smarty_Internal_Template;

/**
 * smarty 関数
 */
interface ITemplateFunction
{
	/**
	 * エラー表示。
	 *
	 * @param array<string,string> $params
	 * @param Smarty_Internal_Template $smarty
	 * @return string HTML
	 */
	public function functionBody(array $params, Smarty_Internal_Template $smarty): string;
}


