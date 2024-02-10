<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

use PeServer\Core\Collection\Arr;
use PeServer\Core\Text;
use PeServer\Core\Html\CodeHighlighter;
use PeServer\Core\Mvc\Template\Plugin\TemplateBlockFunctionBase;
use PeServer\Core\Mvc\Template\Plugin\TemplatePluginArgument;

class CodeFunction extends TemplateBlockFunctionBase
{
	public function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}

	#region TemplateBlockFunctionBase

	public function getFunctionName(): string
	{
		return 'code';
	}

	protected function functionBlockBodyImpl(string $content): string
	{
		$language = Arr::getOr($this->params, 'language', Text::EMPTY);
		$numbers = (string)($this->params['numbers'] ?? '');

		$codeHighlighter = new CodeHighlighter();

		$lineNumbers = $codeHighlighter->toNumbers($numbers);

		$html = $codeHighlighter->toHtml($language, $content, $lineNumbers);

		return $html;
	}

	#endregion
}
