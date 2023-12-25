<?php

declare(strict_types=1);

namespace PeServerUT\Core\Http\Client;

use PeServer\Core\Binary;
use PeServer\Core\Encoding;
use PeServer\Core\Http\Client\JsonContent;
use PeServer\Core\Mime;
use PeServer\Core\Text;
use PeServerTest\TestClass;

class JsonContentTest extends TestClass
{
	public function test_default()
	{
		$jc = new JsonContent(["a" => "b"]);
		$this->assertJson($jc->toBody()->raw);
		$this->assertSame(Mime::JSON, $jc->toHeader()->getContentType()->mime);
	}
}
