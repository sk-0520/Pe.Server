<?php

declare(strict_types=1);

namespace PeServer\Core\Html;

use \DOMNode;
use PeServer\Core\Html\HtmlDocument;
use PeServer\Core\Html\HtmlNodeBase;
use PeServer\Core\Throws\HtmlDocumentException;

abstract class HtmlElementBase extends HtmlNodeBase
{
	protected function __construct(HtmlDocument $document, DOMNode $current)
	{
		parent::__construct($document, $current);
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

		$this->currentNode->appendChild($node);
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

	public function addComment(string $comment): HtmlComment
	{
		$node = $this->document->raw->createComment($comment);
		if ($node === false) { // @phpstan-ignore-line
			throw new HtmlDocumentException();
		}

		$this->appendChild($node);

		return new HtmlComment($this->document, $node);
	}

	public function addText(string $text): HtmlText
	{
		$node = $this->document->raw->createTextNode($text);
		if ($node === false) { // @phpstan-ignore-line
			throw new HtmlDocumentException();
		}

		$this->appendChild($node);

		return new HtmlText($this->document, $node);
	}

	public abstract function path(): HtmlXPath;
}
