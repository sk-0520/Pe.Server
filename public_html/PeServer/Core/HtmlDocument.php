<?php

declare(strict_types=1);

namespace PeServer\Core;

use DOMNode;
use DOMText;
use DOMComment;
use DOMElement;
use DOMDocument;
use PeServer\Core\HtmlElement;
use PeServer\Core\Throws\HtmlDocumentException;

/**
 * DOMDocument のラッパー。
 *
 * JSでもそうだけどなんでDOMは地味に使い辛いんかね。
 */
class HtmlDocument extends HtmlBase
{
	/**
	 * 生で使用する用。
	 */
	public DOMDocument $raw;

	public function __construct()
	{
		$this->raw = new DOMDocument();
		parent::__construct($this, $this->raw);
	}

	public function importNode(HtmlElement|DOMNode $node): HtmlElement|DOMNode
	{
		if ($node instanceof HtmlElement) {
			/** @var DOMElement|false */
			$importedNode = $this->raw->importNode($node->raw, true);
			if ($importedNode === false) {
				throw new HtmlDocumentException();
			}
			return new HtmlElement($this, $importedNode);
		} else {
			$node = $this->raw->importNode($node, true);
			if ($node === false) { // @phpstan-ignore-line
				throw new HtmlDocumentException();
			}
			return $node;
		}
	}

	public function build(): string
	{
		$this->raw->normalize();

		$html = $this->raw->saveHTML();
		if ($html === false) {
			throw new HtmlDocumentException();
		}

		return $html;
	}
}
