<?php

declare(strict_types=1);

namespace PeServerTest;

use PeServer\Core\Binary;
use PeServer\Core\Html\HtmlDocument;
use PeServer\Core\Http\ICallbackContent;
use PeServer\Core\OutputBuffer;
use PeServer\Core\Throws\HtmlDocumentException;

class TestHtmlDocument extends HtmlDocument
{
	public static function new(string|Binary|ICallbackContent|null $html): self
	{
		$result = false;

		$doc = new self();
		if (is_string($html)) {
			$result = $doc->raw->loadHTML($html);
		} else if ($html instanceof Binary) {
			$result = $doc->raw->loadHTML($html->raw);
		} else if ($html instanceof ICallbackContent) {
			$result = $doc->raw->loadHTML(OutputBuffer::get(fn () => $html->output())->raw);
		}
		if ($result == false) {
			throw new HtmlDocumentException();
		}

		return $doc;
	}

	public function getTitle(): string
	{
		$elements = $this->document->path()->path->query('//title');
		return $elements[0]->textContent;
	}
}
