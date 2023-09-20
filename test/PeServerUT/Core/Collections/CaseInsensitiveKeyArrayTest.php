<?php

declare(strict_types=1);

namespace PeServerUT\Core\Collections;

use PeServer\Core\Collections\CaseInsensitiveKeyArray;
use PeServer\Core\Throws\IndexOutOfRangeException;
use PeServer\Core\Throws\KeyNotFoundException;
use PeServer\Core\Throws\NotSupportedException;
use PeServerUT\TestClass;

class CaseInsensitiveKeyArrayTest extends TestClass
{
	public function test_array()
	{
		$ca = new CaseInsensitiveKeyArray();

		$this->assertCount(0, $ca);

		$this->assertFalse(isset($ca['key']));

		$ca[123] = 456;
		$ca[1.2] = 1;
		$ca['KEY'] = 'A';
		$this->assertCount(3, $ca);
		$this->assertTrue(isset($ca[123]));
		$this->assertTrue(isset($ca['1.2']));
		$this->assertTrue(isset($ca['KEY']));
		$this->assertTrue(isset($ca['key']));

		$this->assertSame(456, $ca[123]);
		$this->assertSame(1, $ca['1.2']);
		$this->assertSame(1, $ca['1.2']);
		$this->assertSame('A', $ca['kEy']);

		$dump = [];
		foreach ($ca as $key => $value) {
			$dump[$key] = $value;
		}
		$this->assertSame(456, $dump[123]);
		$this->assertSame(1, $dump['1.2']);
		$this->assertSame('A', $dump['KEY']);
		$this->assertTrue(isset($dump['1.2']));
		$this->assertFalse(isset($dump['key']));


		unset($ca[123]);
		$this->assertFalse(isset($ca[123]));
		unset($ca[1.2]);
		$this->assertFalse(isset($ca['1.2']));
		unset($ca['keY']);
		$this->assertFalse(isset($ca['keY']));
	}

	public static function provider_get_throw()
	{
		return [
			['B', KeyNotFoundException::class],
			[1234, IndexOutOfRangeException::class],
			//[1.23, IndexOutOfRangeException::class],
		];
	}

	/** @dataProvider provider_get_throw */
	public function test_get_throw(mixed $offset, string $exception)
	{
		$ca = new CaseInsensitiveKeyArray([
			'A' => 'b',
			123 => 456,
			'1.2' => 3.4,
		]);
		$this->assertSame('b', $ca['a']);
		$this->assertSame(456, $ca[123]);
		$this->assertSame(3.4, $ca['1.2']);

		$this->expectException($exception);
		$ca[$offset];
		$this->fail();
	}

	public function test_set_null_throw()
	{
		$ca = new CaseInsensitiveKeyArray();
		$ca[''] = 'EMPTY';

		$this->expectException(NotSupportedException::class);
		$ca[] = 'NULL';
		$this->fail();
	}
}
