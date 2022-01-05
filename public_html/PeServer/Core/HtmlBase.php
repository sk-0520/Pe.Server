<?php

declare(strict_types=1);

namespace PeServer\Core;

use DOMText;
use DOMComment;
use DOMElement;
use DOMDocument;
use DOMNode;
use PeServer\Core\HtmlElement;
use PeServer\Core\Throws\HtmlDocumentException;

abstract class HtmlBase
{
	protected HtmlDocument $document;

	/**
	 * 生で使用する用。
	 *
	 * @var DOMNode
	 */
	protected DOMNode $current;

	protected function __construct(HtmlDocument $document, DOMNode $current)
	{
		$this->document = $document;
		$this->current = $current;
	}

	/**
	 * HTML要素を作って追加する。
	 *
	 * @param string $tagName
	 * @return HtmlElement
	 */
	public function addElement(string $tagName): HtmlElement
	{
		$element = $this->document->raw->createElement($tagName);
		if ($element === false) { // @phpstan-ignore-line
			throw new HtmlDocumentException();
		}

		$this->current->appendChild($element);
		return new HtmlElement($this->document, $element);
	}

	public function addComment(string $comment): DOMComment
	{
		$node = $this->document->raw->createComment($comment);
		if ($node === false) { // @phpstan-ignore-line
			throw new HtmlDocumentException();
		}

		$this->current->appendChild($node);

		return $node;
	}

	public function addText(string $text): DOMText
	{
		$node = $this->document->raw->createTextNode($text);
		if ($node === false) { // @phpstan-ignore-line
			throw new HtmlDocumentException();
		}

		$this->current->appendChild($node);

		return $node;
	}
}
