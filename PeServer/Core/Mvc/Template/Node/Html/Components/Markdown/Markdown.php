<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Components\Markdown;

use Override;
use PeServer\Core\Mvc\Markdown as MarkdownParser;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Text;

/**
 * @extends HtmlComponentBase<MarkdownProps>
 */
class Markdown extends HtmlComponentBase
{
	/** @template-use HtmlComponentTrait<MarkdownProps> */
	use HtmlComponentTrait;

	#[Override]
	protected function getNode(): INode
	{
		$className = $this->props->className;
		if (Text::isNullOrWhiteSpace($className)) {
			$className = 'markdown';
		} elseif (!Text::contains($className, 'markdown', false)) {
			$className = 'markdown ' . $className;
		}

		$markdown = new MarkdownParser();
		$markdown->setSafeMode($this->props->safeMode);

		return $this->context->builder->section(
			new HtmlGlobalAttributes(className: $className),
			new TextNode($markdown->build($this->props->content), TextNode::ESCAPE_RAW)
		);
	}
}
