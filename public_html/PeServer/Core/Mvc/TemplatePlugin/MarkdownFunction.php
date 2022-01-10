<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplatePlugin;

use \Smarty;
use \DOMDocument;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Security;
use PeServer\Core\HtmlDocument;
use PeServer\Core\Mvc\Markdown;
use PeServer\Core\Mvc\TemplatePlugin\TemplateBlockFunctionBase;
use PeServer\Core\StringUtility;
use PeServer\Core\TypeConverter;

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
		$className = ArrayUtility::getOr($this->params, 'class', '');
		if (StringUtility::isNullOrWhiteSpace($className)) {
			$className = 'markdown';
		} else if (StringUtility::contains($className, 'markdown', false)) {
			$className = 'markdown ' . $className;
		}

		$isSafeMode = TypeConverter::parseBoolean(ArrayUtility::getOr($this->params, 'safe_mode', true));

		$markdown = new Markdown();
		$markdown->setSafeMode($isSafeMode);
		$result = $markdown->build($content);
		$html = '<div class="' . $className . '">' . $result . '</div>';

		return $html;
	}
}
