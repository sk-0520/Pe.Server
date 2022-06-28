<?php

declare(strict_types=1);

namespace PeServer\Core\Html;

use \DOMComment;
use \DOMDocument;
use \DOMElement;
use \DOMNode;
use \DOMText;
use \DOMXPath;
use \DOMNodeList;
use PeServer\Core\Html\HtmlDocument;
use PeServer\Core\Html\HtmlNodeBase;
use PeServer\Core\StringUtility;
use PeServer\Core\Throws\HtmlXPathException;

/**
 * DOMXPath のラッパー。
 */
class HtmlXPath
{
	public DOMXPath $path;

	public function __construct(
		private HtmlDocument $document,
		private ?HtmlElement $element
	) {
		$this->path = new DOMXPath($document->raw);
	}

	private function node(): ?DOMNode
	{
		if (is_null($this->element)) {
			return null;
		}

		return $this->element->raw;
	}

	//@phpstan-ignore-next-line
	private function toArray(DOMNodeList  $nodeList): array
	{
		$result = [];
		foreach ($nodeList as $node) {
			if ($node instanceof DOMElement) {
				$result[] = new HtmlElement($this->document, $node);
			} else if ($node instanceof DOMText) {
				$result[] = new HtmlText($this->document, $node);
			} else if ($node instanceof DOMComment) {
				$result[] = new HtmlComment($this->document, $node);
			} else if ($node instanceof DOMDocument) {
				$result[] = new HtmlDocument();
			} else {
				throw new HtmlXPathException(StringUtility::dump($node));
			}
		}
		return $result;
	}

	/**
	 * 与えられた XPath 式を評価し、可能であれば結果を返す
	 *
	 * https://www.php.net/manual/ja/domxpath.evaluate.php
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
	 * https://www.php.net/manual/ja/domxpath.query.php
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
}
