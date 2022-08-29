<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

use \Smarty_Internal_Template;
use PeServer\Core\Mvc\Template\Plugin\ITemplateFunction;

/**
 * smarty 関数
 */
interface ITemplateBlockFunction extends ITemplateFunction
{
	#region function

	/**
	 * 関数名取得。
	 *
	 * @return string
	 */
	public function getFunctionName(): string;

	/**
	 * 関数処理出力。
	 *
	 * @param array<string,string> $params
	 * @param mixed $content
	 * @param Smarty_Internal_Template $template
	 * @return string HTML
	 */
	public function functionBlockBody(array $params, mixed $content, Smarty_Internal_Template $template, bool &$repeat): string;

	#endregion
}
