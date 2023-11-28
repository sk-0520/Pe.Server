<?php

declare(strict_types=1);

namespace PeServerUT\Core\Html;

use PeServer\Core\Html\HtmlDocument;
use PeServerTest\TestClass;

class HtmlTextElementTest extends TestClass
{
	public function test_set_get()
	{
		$hd = new HtmlDocument();
		$text = $hd->addText('abc');
		$this->assertSame('abc', $text->get());

		$text->set('<xyz>');
		$this->assertSame('<xyz>', $text->get());
	}
}
