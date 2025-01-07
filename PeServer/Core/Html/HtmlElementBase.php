<?php

declare(strict_types=1);

namespace PeServer\Core\Html;

use DOMNode;
use DOMException;
use PeServer\Core\Html\HtmlDocument;
use PeServer\Core\Html\HtmlNodeBase;
use PeServer\Core\Throws\HtmlException;
use PeServer\Core\Throws\HtmlTagElementException;
use PeServer\Core\Throws\Throws;
use Throwable;

abstract class HtmlElementBase extends HtmlNodeBase
{
	protected function __construct(HtmlDocument $document, DOMNode $current)
	{
		parent::__construct($document, $current);
	}

	#region function

	public function createTagElement(string $tagName): HtmlTagElement
	{
		try {
			$element = $this->document->raw->createElement($tagName);
		} catch (DOMException $ex) {
			Throws::reThrow(HtmlException::class, $ex);
		}

		if ($element === false) {
			throw new HtmlException();
		}

		return new HtmlTagElement($this->document, $element);
	}

	public function createText(string $text): HtmlTextElement
	{
		try {
			$node = $this->document->raw->createTextNode($text);
			return new HtmlTextElement($this->document, $node);
		} catch (Throwable $ex) {
			Throws::reThrow(HtmlException::class, $ex);
		}
	}

	public function createComment(string $text): HtmlCommentElement
	{
		try {
			$node = $this->document->raw->createComment($text);
			return new HtmlCommentElement($this->document, $node);
		} catch (Throwable $ex) {
			Throws::reThrow(HtmlException::class, $ex);
		}
	}

	public function appendChild(HtmlTagElement|HtmlTextElement|HtmlCommentElement|DOMNode $node): void
	{
		if ($node instanceof HtmlTagElement) {
			$node = $node->raw;
		} elseif ($node instanceof HtmlTextElement) {
			$node = $node->raw;
		} elseif ($node instanceof HtmlCommentElement) {
			$node = $node->raw;
		}

		$this->currentNode->appendChild($node);
	}

	/**
	 * HTML要素を作って追加する。
	 *
	 * @param string $tagName
	 * @return HtmlTagElement
	 */
	public function addTagElement(string $tagName): HtmlTagElement
	{
		$node = $this->createTagElement($tagName);

		$this->appendChild($node);

		return $node;
	}

	public function addComment(string $comment): HtmlCommentElement
	{
		$node = $this->createComment($comment);

		$this->appendChild($node);

		return $node;
	}

	public function addText(string $text): HtmlTextElement
	{
		$node = $this->createText($text);

		$this->appendChild($node);

		return $node;
	}

	abstract public function path(): HtmlXPath;

	#endregion
}
