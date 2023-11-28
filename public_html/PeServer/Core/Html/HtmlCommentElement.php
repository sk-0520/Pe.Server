<?php

declare(strict_types=1);

namespace PeServer\Core\Html;

use DOMComment;
use PeServer\Core\Html\HtmlDocument;
use PeServer\Core\Html\HtmlNodeBase;

/**
 * `DOMComment` ラッパー。
 */
final class HtmlCommentElement extends HtmlNodeBase
{
	#region variable

	/**
	 * 生で使用する用。
	 * @readonly
	 */
	public readonly DOMComment $raw;

	#endregion

	public function __construct(HtmlDocument $document, DOMComment $raw)
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
