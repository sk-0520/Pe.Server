<?php

declare(strict_types=1);

namespace PeServer\Core\Html;

use DOMNode;
use \DOMText;
use DOMXPath;
use DOMNodeList;
use PeServer\Core\Html\HtmlDocument;
use PeServer\Core\Html\HtmlNodeBase;
use PeServer\Core\Throws\HtmlXPathException;

/**
 * DOMXPath のラッパー。
 */
class HtmlXPath
{
	public DOMXPath $path;

	public function __construct(
		HtmlDocument $document,
		private ?HtmlElement $element
	) {
		$this->path = new DOMXPath($document->raw);
	}

	private function node(): ?DOMNode
	{
		if(is_null($this->element)) {
			return null;
		}

		return $this->element->raw;
	}

	public function evaluate(string $expression): DOMNodeList
	{
		$nodeList = $this->path->evaluate($expression, $this->node());
		if($nodeList === false) {
			throw new HtmlXPathException();
		}

		return $nodeList;
	}
}
