<?php

declare(strict_types=1);

namespace PeServer\Core\Html;

use \DOMNode;
use \DOMText;
use \DOMComment;
use PeServer\Core\Html\HtmlElement;
use PeServer\Core\Throws\HtmlDocumentException;

abstract class HtmlNodeBase
{
	/**
	 * @readonly
	 */
	protected HtmlDocument $document;

	/**
	 * 生で使用する現在データ。
	 * @readonly
	 */
	protected DOMNode $current;

	protected function __construct(HtmlDocument $document, DOMNode $current)
	{
		$this->document = $document;
		$this->current = $current;
	}
}
