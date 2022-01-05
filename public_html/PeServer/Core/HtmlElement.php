<?php

declare(strict_types=1);

namespace PeServer\Core;

use DOMText;
use DOMComment;
use DOMElement;
use PeServer\Core\Throws\HtmlDocumentException;

/**
 * DOMElement のラッパー。
 */
class HtmlElement extends HtmlBase
{
	/**
	 * 生で使用する用。
	 *
	 * @var DOMElement
	 */
	protected DOMElement $raw;
	public function __construct(HtmlDocument $document, DOMElement $raw)
	{
		parent::__construct($document, $raw);
		$this->raw = $raw;
	}

	public function setAttribute(string $qualifiedName, string $value): void
	{
		$result = $this->raw->setAttribute($qualifiedName, $value);
		if ($result === false) { // @phpstan-ignore-line
			throw new HtmlDocumentException();
		}
	}
}
