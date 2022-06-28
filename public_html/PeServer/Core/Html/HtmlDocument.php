<?php

declare(strict_types=1);

namespace PeServer\Core\Html;

use \DOMNode;
use \DOMText;
use \DOMComment;
use \DOMElement;
use \DOMDocument;
use PeServer\Core\Html\HtmlElement;
use PeServer\Core\Throws\HtmlDocumentException;

/**
 * DOMDocument のラッパー。
 *
 * JSでもそうだけどなんでDOMは地味に使い辛いんかね。
 */
class HtmlDocument extends HtmlElementBase
{
	/**
	 * 生で使用する用。
	 */
	public DOMDocument $raw;

	public function __construct()
	{
		libxml_use_internal_errors(true);

		$this->raw = new DOMDocument();
		parent::__construct($this, $this->raw);
	}

	public static function load(string $html): HtmlDocument
	{
		$doc = new HtmlDocument();
		$result = $doc->raw->loadHTML($html);
		if ($result == false) {
			throw new HtmlDocumentException();
		}

		return $doc;
	}

	public function importNode(HtmlElement $node): HtmlElement
	{
		/** @var DOMElement|false */
		$importedNode = $this->raw->importNode($node->raw, true);
		if ($importedNode === false) {
			throw new HtmlDocumentException();
		}
		return new HtmlElement($this, $importedNode);
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

	final public function path(): HtmlXPath
	{
		return new HtmlXPath($this->document, null);
	}
}
