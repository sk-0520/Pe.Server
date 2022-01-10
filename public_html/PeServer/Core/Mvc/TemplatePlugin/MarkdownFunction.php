<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplatePlugin;

use \Smarty;
use \DOMDocument;
use PeServer\App\Models\Domains\UserLevel;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Security;
use PeServer\Core\HtmlDocument;
use PeServer\Core\Mvc\Markdown;
use PeServer\Core\Mvc\TemplatePlugin\TemplateBlockFunctionBase;
use PeServer\Core\StringUtility;

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

		$level = ArrayUtility::getOr($this->params, 'level', UserLevel::USER);

		$markdown = new Markdown();
		$markdown->setLevel($level);
		$result = $markdown->build($content);
		$html = '<div class="' . $className . '">' . $result . '</div>';

		return $html;
	}
}
