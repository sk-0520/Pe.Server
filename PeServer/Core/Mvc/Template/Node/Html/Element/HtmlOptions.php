<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\ElementOptions;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLBodyAttributes;
use PeServer\Core\Mvc\Template\Node\INode;

class HtmlElementOptions extends ElementOptions
{
	public function __construct(
		bool $selfClosing
	) {
		parent::__construct($selfClosing);
	}

	#region function

	public static function block(): self
	{
		return new HtmlElementOptions(false);
	}

	public static function inline(): self
	{
		return new HtmlElementOptions(true);
	}

	#endregion
}
