<?php

declare(strict_types=1);

namespace PeServerTest\Core\Http;

use PeServer\Core\Http\HttpHeader;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Throws\KeyNotFoundException;
use PeServerTest\TestClass;

class HttpHeaderTest extends TestClass
{
	public function test_setValue_empty_throw()
	{
		$hh = new HttpHeader();
		$this->expectException(ArgumentException::class);
		$this->expectExceptionMessage('$name');
		$hh->setValue(' ', 'VALUE');
	}

	public function test_setValue_redirect_throw()
	{
		$hh = new HttpHeader();
		$this->expectException(ArgumentException::class);
		$this->expectExceptionMessage('$name: setRedirect()');
		$hh->setValue('LoCaTiOn', 'VALUE');
	}

	public function test_setValue_getValues()
	{
		$hh = new HttpHeader();
		$hh->setValue('NAME', 'VALUE');
		$actual1 = $hh->getValues('NAME');

		$this->assertSame(['VALUE'], $actual1);

		$hh->setValue('NAME', 'VALUE2');
		$actual2 = $hh->getValues('NAME');

		$this->assertSame(['VALUE2'], $actual2);
	}


	public function test_setValues_getValues()
	{
		$hh = new HttpHeader();
		$hh->setValues('NAME', ['VALUE', 'ADD']);
		$actual1 = $hh->getValues('NAME');

		$this->assertSame(['VALUE', 'ADD'], $actual1);

		$hh->setValues('NAME', ['VALUE2', 'ADD2']);
		$actual2 = $hh->getValues('NAME');

		$this->assertSame(['VALUE2', 'ADD2'], $actual2);
	}

	public function test_addValue_getValues()
	{
		$hh = new HttpHeader();
		$hh->addValue('NAME', 'VALUE');
		$actual1 = $hh->getValues('NAME');

		$this->assertSame(['VALUE'], $actual1);

		$hh->addValue('NAME', 'VALUE2');
		$actual2 = $hh->getValues('NAME');

		$this->assertSame(['VALUE', 'VALUE2'], $actual2);
	}

	public function test_existsHeader()
	{
		$hh = new HttpHeader();
		$hh->addValue('NAME', 'VALUE');

		$this->assertFalse($hh->existsHeader('   '));
		$this->assertFalse($hh->existsHeader('location'));

		$this->assertTrue($hh->existsHeader('NAME'));
		$this->assertFalse($hh->existsHeader('NAME2'));
	}

	public function test_getValues_throw()
	{
		$hh = new HttpHeader();
		$this->expectException(KeyNotFoundException::class);
		$hh->getValues('NAME');
	}

	public function test_redirect()
	{
		$hh = new HttpHeader();

		$this->assertFalse($hh->existsRedirect());
		$this->assertFalse($hh->clearRedirect());

		$hh->setRedirect('url', null);
		$this->assertTrue($hh->existsRedirect());
		$actual1 = $hh->getRedirect();
		$this->assertSame('url', $actual1->url);
		$this->assertEquals(HttpStatus::moved(), $actual1->status, '生成オブジェクト自体が異なるので等価比較');
		$this->assertTrue($hh->clearRedirect());
		$this->assertFalse($hh->existsRedirect());

		$hh->setRedirect('URL', HttpStatus::found());
		$this->assertTrue($hh->existsRedirect());
		$actual2 = $hh->getRedirect();
		$this->assertSame('URL', $actual2->url);
		$this->assertEquals(HttpStatus::found(), $actual2->status, '生成オブジェクト自体が異なるので等価比較');
		$this->assertTrue($hh->clearRedirect());
		$this->assertFalse($hh->existsRedirect());

		$this->expectException(InvalidOperationException::class);
		$hh->getRedirect();
	}
}
