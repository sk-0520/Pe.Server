<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Components\Code;

use Override;
use PeServer\Core\Html\CodeHighlighter;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\TextNode;

/** @extends HtmlComponentBase<CodeProps> */
class Code extends HtmlComponentBase
{
	/** @template-use HtmlComponentTrait<CodeProps> */
	use HtmlComponentTrait;

	#[Override]
	protected function getNode(): INode
	{
		$highlighter = new CodeHighlighter();
		return new TextNode($highlighter->toHtml(
			$this->props->language,
			$this->props->content,
			$highlighter->toNumbers($this->props->numbers)
		), TextNode::ESCAPE_RAW);
	}
}
