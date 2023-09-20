<?php

declare(strict_types=1);

namespace PeServerUT\Core\Http\Client;

use PeServer\Core\Binary;
use PeServer\Core\Collections\Dictionary;
use PeServer\Core\Encoding;
use PeServer\Core\Http\Client\FormUrlEncodedContent;
use PeServer\Core\Mime;
use PeServer\Core\Text;
use PeServer\Core\Web\UrlEncoding;
use PeServerUT\Data;
use PeServerUT\TestClass;

class FormUrlEncodedContentTest extends TestClass
{
	public function test_default()
	{
		$bc = new FormUrlEncodedContent(Dictionary::create(['a' => 'b', 'A' => 'B']));
		$this->assertSame("a=b&A=B", $bc->toBody()->getRaw());
		$this->assertSame(Mime::FORM, $bc->toHeader()->getContentType()->mime);
	}

	public function test_encoding()
	{
		$input = Dictionary::create(['name' => '1 + 1 = ~']);

		$default = new FormUrlEncodedContent($input);
		$this->assertSame("name=1+%2B+1+%3D+%7E", $default->toBody()->getRaw());

		$custom = new FormUrlEncodedContent($input, UrlEncoding::createDefault());
		$this->assertSame("name=1%20%2B%201%20%3D%20~", $custom->toBody()->getRaw());
	}
}
