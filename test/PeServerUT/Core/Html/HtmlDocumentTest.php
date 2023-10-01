<?php

declare(strict_types=1);

namespace PeServerUT\Core\Html;

use PeServer\Core\Html\HtmlDocument;
use PeServer\Core\Throws\HtmlDocumentException;
use PeServerTest\TestClass;

class HtmlDocumentTest extends TestClass
{
	public function test_load()
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
			HtmlDocument::load($test);
		}
		$this->success();
	}

	public static function provider_load_throw()
	{
		return [
			[''],
		];
	}

	/** @dataProvider provider_load_throw */
	public function test_load_throw($html)
	{
		$this->expectException(HtmlDocumentException::class);
		HtmlDocument::load($html);
		$this->fail();
	}
}
