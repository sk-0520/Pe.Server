<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

use PeServer\Core\Collections\Arr;
use PeServer\Core\Mvc\Markdown;
use PeServer\Core\Text;
use PeServer\Core\TypeUtility;
use PeServer\Core\Mvc\Template\Plugin\TemplatePluginArgument;
use PeServer\Core\Mvc\Template\Plugin\TemplateBlockFunctionBase;

class MarkdownFunction extends TemplateBlockFunctionBase
{
	public function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}

	#region TemplateBlockFunctionBase

	public function getFunctionName(): string
	{
		return 'markdown';
	}

	protected function functionBlockBodyImpl(string $content): string
	{
		/** @var string */
		$className = $this->params['class'] ?? Text::EMPTY;
		if (Text::isNullOrWhiteSpace($className)) {
			$className = 'markdown';
		} elseif (!Text::contains($className, 'markdown', false)) {
			$className = 'markdown ' . $className;
		}

		$isSafeMode = TypeUtility::parseBoolean($this->params['safe_mode'] ?? true);

		$markdown = new Markdown();
		$markdown->setSafeMode($isSafeMode);
		$result = $markdown->build($content);
		$html = '<section class="' . $className . '">' . $result . '</section>';

		return $html;
	}

	#endregion
}
