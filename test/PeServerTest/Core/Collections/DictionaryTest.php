<?php

declare(strict_types=1);

namespace PeServerTest\Core\Collections;

use PeServer\Core\Collections\Dictionary;
use PeServer\Core\Throws\ArgumentException;

use PeServer\Core\Throws\IndexOutOfRangeException;
use PeServer\Core\TypeUtility;
use PeServerTest\TestClass;
use TypeError;


class DictionaryTest extends TestClass
{
	function test_offsetExists()
	{
		$dic = Dictionary::create(['a' => 'A', 'b' => 'B', '0' => 'o']);
		$this->assertFalse(isset($dic['A']));
		$this->assertTrue(isset($dic['a']));
		$this->assertTrue(isset($dic['b']));
		$this->assertTrue(isset($dic['0']));
	}

	function test_offsetExists_throw()
	{
		$dic = Dictionary::create(['a' => 'A', 'b' => 'B', '0' => 'o']);
		$this->expectException(TypeError::class);
		isset($dic[0]);
		$this->fail();
	}

	function test_offsetGet()
	{
		$dic = Dictionary::create(['a' => 'A', 'b' => 'B', '0' => 'o']);
		$this->assertSame('A', $dic['a']);
		$this->assertSame('B', $dic['b']);
		$this->assertSame('o', $dic['0']);
	}

	function test_offsetGet_null_throw()
	{
		$dic = Dictionary::create(['a' => 'A', 'b' => 'B', '0' => 'o']);
		$this->expectException(TypeError::class);
		$dic[null];
	}

	function test_offsetGet_not_string_throw()
	{
		$dic = Dictionary::create(['a' => 'A', 'b' => 'B', '0' => 'o']);
		$this->expectException(TypeError::class);
		$dic[0];
	}

	function test_offsetSet()
	{
		$dic = Dictionary::create(['a' => 'A', 'b' => 'B', '0' => 'o']);
		$this->assertSame('A', $dic['a']);
		$this->assertSame('B', $dic['b']);
		$this->assertSame('o', $dic['0']);

		$dic['a'] = '10';
		$dic['b'] = $dic['b'] . $dic['b'];
		$dic['0'] .= 'o';

		$this->assertSame('10', $dic['a']);
		$this->assertSame('BB', $dic['b']);
		$this->assertSame('oo', $dic['0']);
	}

	function test_offsetUnset()
	{
		$dic = Dictionary::create(['a' => 'A', 'b' => 'B', '0' => 'o']);

		unset($dic['a']);
		$this->assertSame(2, $dic->count());

		unset($dic['b']);
		$this->assertSame(1, $dic->count());

		unset($dic['0']);
		$this->assertSame(0, $dic->count());
	}
}
