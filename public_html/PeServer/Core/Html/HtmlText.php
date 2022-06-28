<?php

declare(strict_types=1);

namespace PeServer\Core\Html;

use \DOMText;
use PeServer\Core\Html\HtmlNodeBase;
use PeServer\Core\Html\HtmlDocument;

/**
 * DOMText のラッパー。
 */
class HtmlText extends HtmlNodeBase
{
	public DOMText $raw;

	public function __construct(HtmlDocument $document, DOMText $raw)
	{
		parent::__construct($document, $raw);
		$this->raw = $raw;
	}
}
