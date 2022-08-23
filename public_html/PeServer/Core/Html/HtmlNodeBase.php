<?php

declare(strict_types=1);

namespace PeServer\Core\Html;

use \DOMNode;

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