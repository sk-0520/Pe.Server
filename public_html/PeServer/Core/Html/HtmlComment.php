<?php

declare(strict_types=1);

namespace PeServer\Core\Html;

use \DOMComment;
use PeServer\Core\Html\HtmlNodeBase;
use PeServer\Core\Html\HtmlDocument;

/**
 * DOMComment のラッパー。
 */
final class HtmlComment extends HtmlNodeBase
{
	/**
	 * 生で使用する用。
	 * @readonly
	*/
	public DOMComment $raw;

	public function __construct(HtmlDocument $document, DOMComment $raw)
	{
		parent::__construct($document, $raw);
		$this->raw = $raw;
	}
}
