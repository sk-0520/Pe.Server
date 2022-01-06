<?php

declare(strict_types=1);

namespace PeServer\Core;

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
	 *
	 * @var DOMDocument
	 */
	public DOMDocument $raw;

	public function __construct()
	{
		$this->raw = new DOMDocument();
		parent::__construct($this, $this->raw);
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
