<?php

declare(strict_types=1);

namespace PeServer\Core\Html;

use DOMComment;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;
use DOMText;
use DOMXPath;
use PeServer\Core\Html\HtmlComment;
use PeServer\Core\Html\HtmlDocument;
use PeServer\Core\Html\HtmlElement;
use PeServer\Core\Html\HtmlNodeBase;
use PeServer\Core\Html\HtmlText;
use PeServer\Core\Text;
use PeServer\Core\Throws\HtmlXPathException;

/**
 * `DOMXPath` ラッパー。
 */
class HtmlXPath
{
	#region variable

	/**
	 * @readonly
	 */
	public readonly DOMXPath $path;

	#endregion

	public function __construct(
		/** @readonly */
		private HtmlDocument $document,
		/** @readonly */
		private ?HtmlElement $element
	) {
		$this->path = new DOMXPath($document->raw);
	}

	#region function

	private function node(): ?DOMNode
	{
		if ($this->element === null) {
			return null;
		}

		return $this->element->raw;
	}

	//@phpstan-ignore-next-line
	private function toArray(DOMNodeList $nodeList): array
	{
		$result = [];
		foreach ($nodeList as $node) {
			if ($node instanceof DOMElement) {
				$result[] = new HtmlElement($this->document, $node);
			} elseif ($node instanceof DOMText) {
				$result[] = new HtmlText($this->document, $node);
			} elseif ($node instanceof DOMComment) {
				$result[] = new HtmlComment($this->document, $node);
			} elseif ($node instanceof DOMDocument) {
				$result[] = new HtmlDocument();
			} else {
				throw new HtmlXPathException(Text::dump($node));
			}
		}
		return $result;
	}

	/**
	 * 与えられた XPath 式を評価し、可能であれば結果を返す
	 *
	 * https://www.php.net/manual/domxpath.evaluate.php
	 *
	 * @param string $expression
	 * @return HtmlNodeBase[]
	 * @throws HtmlXPathException
	 */
	public function evaluate(string $expression): array
	{
		$nodeList = $this->path->evaluate($expression, $this->node());
		if ($nodeList === false) {
			throw new HtmlXPathException();
		}

		return $this->toArray($nodeList);
	}


	/**
	 * 与えられた XPath 式を評価する
	 *
	 * https://www.php.net/manual/domxpath.query.php
	 *
	 * @param string $expression
	 * @return HtmlNodeBase[]
	 * @throws HtmlXPathException
	 */
	public function query(string $expression): array
	{
		$nodeList = $this->path->query($expression, $this->node());
		if ($nodeList === false) {
			throw new HtmlXPathException();
		}

		return $this->toArray($nodeList);
	}

	#endregion
}
