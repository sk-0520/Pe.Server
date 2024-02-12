<?php

declare(strict_types=1);

namespace PeServer\Core\Html;

use DOMNode;
use PeServer\Core\Html\HtmlDocument;

abstract class HtmlNodeBase
{
	#region variable

	/**
	 */
	protected readonly HtmlDocument $document;

	/**
	 * 生で使用する現在データ。
	 */
	protected readonly DOMNode $currentNode;

	#endregion

	protected function __construct(HtmlDocument $document, DOMNode $currentNode)
	{
		$this->document = $document;
		$this->currentNode = $currentNode;
	}
}
