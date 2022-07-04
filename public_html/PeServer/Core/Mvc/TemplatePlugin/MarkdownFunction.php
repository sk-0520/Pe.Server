<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplatePlugin;

use PeServer\Core\ArrayUtility;
use PeServer\Core\InitialValue;
use PeServer\Core\Mvc\Markdown;
use PeServer\Core\StringUtility;
use PeServer\Core\TypeConverter;
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

	/**
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 */
	protected function functionBlockBodyImpl(string $content): string
	{
		/** @var string */
		$className = ArrayUtility::getOr($this->params, 'class', InitialValue::EMPTY_STRING);
		if (StringUtility::isNullOrWhiteSpace($className)) {
			$className = 'markdown';
		} else if (!StringUtility::contains($className, 'markdown', false)) {
			$className = 'markdown ' . $className;
		}

		$isSafeMode = TypeConverter::parseBoolean(ArrayUtility::getOr($this->params, 'safe_mode', true));

		$markdown = new Markdown();
		$markdown->setSafeMode($isSafeMode);
		$result = $markdown->build($content);
		$html = '<section class="' . $className . '">' . $result . '</section>';

		return $html;
	}
}
