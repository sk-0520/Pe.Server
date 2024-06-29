<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

use Smarty\Template;

/**
 * smarty 関数
 */
interface ITemplateFunction
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
	 * @param Template $smarty
	 * @return string HTML
	 */
	public function functionBody(array $params, Template $smarty): string;

	#endregion
}
