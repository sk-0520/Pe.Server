<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template;

use PeServer\Core\Mvc\Template\TemplateParameter;

/**
 * View側のテンプレート処理。
 *
 * 初期化の呼び出しが必須。
 */
abstract class TemplateBase
{
	protected function __construct(
		protected TemplateOptions $options
	) {
	}

	#region function

	/**
	 * View描画処理。
	 *
	 * @param string $templateName テンプレート名。
	 * @param TemplateParameter $parameter パラメータ。
	 * @return string
	 */
	abstract public function build(string $templateName, TemplateParameter $parameter): string;

	#endregion
}
