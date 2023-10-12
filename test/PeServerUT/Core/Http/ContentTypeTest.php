<?php

declare(strict_types=1);

namespace PeServerUT\Core\Http;

use PeServer\Core\Encoding;
use PeServer\Core\Http\ContentType;
use PeServerTest\TestClass;

class ContentTypeTest extends TestClass
{
	public function test_create()
	{
		$actual1 = ContentType::create('mime');
		$this->assertSame('mime', $actual1->mime);
		$this->assertSame(Encoding::getDefaultEncoding()->name, $actual1->encoding->name);

		$actual2 = ContentType::create('', Encoding::getShiftJis());
		$this->assertSame('', $actual2->mime);
		$this->assertSame(Encoding::getShiftJis()->name, $actual2->encoding->name);
	}

	public function test_from()
	{
		$actual1 = ContentType::from('mime');
		$this->assertSame('mime', $actual1->mime);

		$actual2 = ContentType::from('mime! ; charset=CP932 ');
		$this->assertSame('mime!', $actual2->mime);
		$this->assertSame('CP932', $actual2->encoding->name);

		$actual2 = ContentType::from('mime? ; boundary=*** ');
		$this->assertSame('mime?', $actual2->mime);
		$this->assertSame('***', $actual2->boundary);

		$actual3 = ContentType::from('mime@ ; charset=UTF-32 ; boundary=*** ');
		$this->assertSame('mime@', $actual3->mime);
		$this->assertSame('UTF-32', $actual3->encoding->name);
		$this->assertSame('***', $actual3->boundary);
	}
}
