<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

use \Smarty_Internal_Template;

/**
 * smarty 関数
 */
interface ITemplateModifier
{
	#region function

	/**
	 * 修正子名取得。
	 *
	 * @return string
	 */
	public function getModifierName(): string;

	/**
	 * 修正子処理出力。
	 *
	 * @param array<string,string> $params
	 * @param Smarty_Internal_Template $smarty
	 * @return string HTML
	 */
	public function modifierBody(mixed $value, array $params, Smarty_Internal_Template $smarty): string;

	#endregion
}
