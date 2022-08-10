<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplatePlugin;

use PeServer\Core\ArrayUtility;
use PeServer\Core\DefaultValue;
use PeServer\Core\Mvc\Markdown;
use PeServer\Core\Text;
use PeServer\Core\TypeUtility;
use PeServer\Core\Mvc\TemplatePlugin\TemplatePluginArgument;
use PeServer\Core\Mvc\TemplatePlugin\TemplateBlockFunctionBase;

class MarkdownFunction extends TemplateBlockFunctionBase
{
	public function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}

	public function getFunctionName(): string
	{
		return 'markdown';
	}

	protected function functionBlockBodyImpl(string $content): string
	{
		/** @var string */
		$className = ArrayUtility::getOr($this->params, 'class', DefaultValue::EMPTY_STRING);
		if (Text::isNullOrWhiteSpace($className)) {
			$className = 'markdown';
		} else if (!Text::contains($className, 'markdown', false)) {
			$className = 'markdown ' . $className;
		}

		$isSafeMode = TypeUtility::parseBoolean(ArrayUtility::getOr($this->params, 'safe_mode', true));

		$markdown = new Markdown();
		$markdown->setSafeMode($isSafeMode);
		$result = $markdown->build($content);
		$html = '<section class="' . $className . '">' . $result . '</section>';

		return $html;
	}
}
