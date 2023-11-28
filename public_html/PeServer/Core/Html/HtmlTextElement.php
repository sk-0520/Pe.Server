<?php

declare(strict_types=1);

namespace PeServer\Core\Html;

use DOMText;
use PeServer\Core\Html\HtmlDocument;
use PeServer\Core\Html\HtmlNodeBase;

/**
 * `DOMText` ラッパー。
 *
 */
final class HtmlTextElement extends HtmlNodeBase
{
	#region variable

	/**
	 * @readonly
	 */
	public readonly DOMText $raw;

	#endregion

	public function __construct(HtmlDocument $document, DOMText $raw)
	{
		parent::__construct($document, $raw);
		$this->raw = $raw;
	}

	#region function

	public function get(): string
	{
		return $this->raw->textContent;
	}

	public function set(string $value): void
	{
		$this->raw->textContent = $value;
	}

	#endregion
}
