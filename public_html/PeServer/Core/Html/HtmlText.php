<?php

declare(strict_types=1);

namespace PeServer\Core\Html;

use DOMText;
use PeServer\Core\Html\HtmlDocument;
use PeServer\Core\Html\HtmlNodeBase;

/**
 * `DOMText` ラッパー。
 */
final class HtmlText extends HtmlNodeBase
{
	#region variable

	/**
	 * @readonly
	 */
	public DOMText $raw;

	#endregion

	public function __construct(HtmlDocument $document, DOMText $raw)
	{
		parent::__construct($document, $raw);
		$this->raw = $raw;
	}
}
