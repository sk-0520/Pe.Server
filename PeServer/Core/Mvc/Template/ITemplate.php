<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template;

use PeServer\Core\Mvc\Template\TemplateParameter;

interface ITemplate
{
	#region function

	/**
	 * View描画処理。
	 *
	 * @param string $templateName テンプレート名。
	 * @param TemplateParameter $parameter パラメータ。
	 * @return string
	 */
	public function build(string $templateName, TemplateParameter $parameter): string;

	#endregion
}
