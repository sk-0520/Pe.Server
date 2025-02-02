<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Component\Layout;

use PeServer\Core\Mvc\Template\Node\ComponentBase;
use PeServer\Core\Mvc\Template\Node\Content;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLHtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\Html\Tag;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Throws\NotImplementedException;

class Layout extends ComponentBase
{
	public function __construct(Content $content, public LayoutProps $props, Tag $tag)
	{
		parent::__construct($content, $props, $tag);
	}

	#region ComponentBase

	protected function build(): INode
	{
		return $this->tag->html(
			new HTMLHtmlAttributes([
				"lang" => 0.1 //$this->props->language
				//"xmlns" => ""
			]),
			new HtmlContent()
		);
	}

	#endregion
}
