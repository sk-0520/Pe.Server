<?php

declare(strict_types=1);

namespace PeServerUT\Core\Html;

use PeServer\Core\Html\HtmlDocument;
use PeServer\Core\Throws\HtmlDocumentException;
use PeServerTest\TestClass;

class HtmlDocumentTest extends TestClass
{
	public function test_constructor_html()
	{
		$tests = [
			"a",
			"<a><a>",
			"<a>",
			"<a",
			"<>",
			"<><>",
			"<a></a>",
			"<a><//a>",
		];
		foreach ($tests as $test) {
			new HtmlDocument($test);
		}
		$this->success();
	}

	public static function provider_constructor_html_throw()
	{
		return [
			[''],
		];
	}

	/** @dataProvider provider_constructor_html_throw */
	public function test_constructor_html_throw($html)
	{
		$this->expectException(HtmlDocumentException::class);
		new HtmlDocument($html);
		$this->fail();
	}
}
