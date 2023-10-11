<?php

declare(strict_types=1);

namespace PeServerUT\Core\Web;

use PeServer\Core\Encoding;
use PeServer\Core\Web\UrlEncodeKind;
use PeServer\Core\Web\UrlEncoding;
use PeServerTest\TestClass;

class UrlEncodingTest extends TestClass
{
	public function test_createDefault()
	{
		$ue = UrlEncoding::createDefault();

		$this->assertSame(UrlEncodeKind::Rfc3986, $ue->url);
		$this->assertSame(Encoding::ENCODE_UTF8, $ue->string->name);
	}
}
