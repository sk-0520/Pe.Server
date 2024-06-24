<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

use Smarty\Template;
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
	 * @param Template $template
	 * @return string HTML
	 */
	public function functionBlockBody(array $params, mixed $content, Template $template, bool &$repeat): string;

	#endregion
}
