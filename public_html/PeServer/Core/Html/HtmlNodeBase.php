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
	protected DOMNode $currentNode;

	protected function __construct(HtmlDocument $document, DOMNode $currentNode)
	{
		$this->document = $document;
		$this->currentNode = $currentNode;
	}
}
