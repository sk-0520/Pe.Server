<?php

declare(strict_types=1);

namespace PeServer\Core;

use \DOMText;
use \DOMComment;
use \DOMNode;
use PeServer\Core\HtmlElement;
use PeServer\Core\Throws\HtmlDocumentException;

abstract class HtmlBase
{
	protected HtmlDocument $document;

	/**
	 * 生で使用する現在データ。
	 */
	protected DOMNode $current;

	protected function __construct(HtmlDocument $document, DOMNode $current)
	{
		$this->document = $document;
		$this->current = $current;
	}

	public function createElement(string $tagName): HtmlElement
	{
		$element = $this->document->raw->createElement($tagName);
		if ($element === false) { // @phpstan-ignore-line
			throw new HtmlDocumentException();
		}

		return new HtmlElement($this->document, $element);
	}

	public function appendChild(HtmlElement|DOMNode $node): void
	{
		if ($node instanceof HtmlElement) {
			$node = $node->raw;
		}

		$this->current->appendChild($node);
	}

	/**
	 * HTML要素を作って追加する。
	 *
	 * @param string $tagName
	 * @return HtmlElement
	 */
	public function addElement(string $tagName): HtmlElement
	{
		$element = $this->createElement($tagName);

		$this->appendChild($element);
		return $element;
	}

	public function addComment(string $comment): DOMComment
	{
		$node = $this->document->raw->createComment($comment);
		if ($node === false) { // @phpstan-ignore-line
			throw new HtmlDocumentException();
		}

		$this->appendChild($node);

		return $node;
	}

	public function addText(string $text): DOMText
	{
		$node = $this->document->raw->createTextNode($text);
		if ($node === false) { // @phpstan-ignore-line
			throw new HtmlDocumentException();
		}

		$this->appendChild($node);

		return $node;
	}
}
