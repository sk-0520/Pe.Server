<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\ElementOptions;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLBodyAttributes;
use PeServer\Core\Mvc\Template\Node\INode;

class HtmlElementOptions extends ElementOptions
{
	public function __construct(
		bool $isInline,
		bool $selfClosing
	) {
		parent::__construct($isInline, $selfClosing);
	}

	#region function

	/**
	 * ブロック要素。
	 * @param bool $selfClosing 自己終了タグか。
	 * @return HtmlElementOptions
	 */
	public static function block(bool $selfClosing): self
	{
		return new HtmlElementOptions(false, $selfClosing);
	}

	/**
	 * インライン要素。
	 * @param bool $selfClosing 自己終了タグか。
	 * @return HtmlElementOptions
	 */
	public static function inline(bool $selfClosing): self
	{
		return new HtmlElementOptions(true, $selfClosing);
	}

	#endregion
}
