<?php

declare(strict_types=1);

namespace PeServer\Core\Html;

use DOMDocument;
use DOMElement;
use PeServer\Core\Html\HtmlTagElement;
use PeServer\Core\Html\HtmlElementBase;
use PeServer\Core\Html\HtmlXPath;
use PeServer\Core\Throws\HtmlDocumentException;
use PeServer\Core\Throws\Throws;
use ValueError;

libxml_use_internal_errors(true);

/**
 * `DOMDocument` ラッパー。
 *
 * JSでもそうだけどなんでDOMは地味に使い辛いんかね。
 */
class HtmlDocument extends HtmlElementBase
{
	#region variable

	/**
	 * 生で使用する用。
	 * @readonly
	 */
	public readonly DOMDocument $raw;

	#endregion

	public function __construct(?string $html = null)
	{
		$this->raw = new DOMDocument();
		parent::__construct($this, $this->raw);

		if ($html !== null) {
			$result = Throws::wrap(ValueError::class, HtmlDocumentException::class, fn () => $this->raw->loadHTML($html));
			if ($result == false) {
				throw new HtmlDocumentException();
			}
		}
	}

	#region function

	public function importNode(HtmlTagElement $node): HtmlTagElement
	{
		/** @var DOMElement|false */
		$importedNode = $this->raw->importNode($node->raw, true);
		if ($importedNode === false) {
			throw new HtmlDocumentException();
		}
		return new HtmlTagElement($this, $importedNode);
	}

	/**
	 * `DOMDocument::saveHTML` ラッパー。
	 * @return string
	 * @throws HtmlDocumentException
	 * @see https://www.php.net/manual/domdocument.savehtml.php
	 */
	public function build(): string
	{
		$this->raw->normalize();

		$html = $this->raw->saveHTML();
		if ($html === false) {
			throw new HtmlDocumentException();
		}

		return $html;
	}

	/**
	 * `DOMDocument::saveHTMLFile` ラッパー。
	 * @param string $path
	 * @throws HtmlDocumentException
	 * @see https://www.php.net/manual/domdocument.savehtmlfile.php
	 */
	public function save(string $path): void
	{
		$length = $this->raw->saveHTMLFile($path);
		if ($length === false) {
			throw new HtmlDocumentException();
		}
	}

	#endregion

	#region HtmlElementBase

	final public function path(): HtmlXPath
	{
		return new HtmlXPath($this->document, null);
	}

	#endregion
}
