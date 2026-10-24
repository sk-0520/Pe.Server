<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node;

use PeServer\Core\Mvc\Template\Node\BuildOptions;
use PeServer\Core\Mvc\Template\Node\Comment;
use PeServer\Core\Throws\ArgumentException;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class CommentTest extends TestClass
{
	public static function provider_toString(): array
	{
		return [
			['<!-- comment -->', 'comment'],
			['<!-- comment -->', '  comment  '],
			['<!-- -->', ''],
			['<!-- -->', '   '],
			['<!-- <b>&</b> -->', '<b>&</b>'],
			['<!--' . PHP_EOL . 'a' . PHP_EOL . 'b' . PHP_EOL . '-->', 'a' . PHP_EOL . 'b'],
			['<!--' . PHP_EOL . 'a' . PHP_EOL . 'b' . PHP_EOL . '-->', '  a  ' . PHP_EOL . '  b  '],
		];
	}

	#[DataProvider('provider_toString')]
	public function test_toString(string $expected, string $value): void
	{
		$test = new Comment($value);
		$this->assertSame($expected, $test->toString(0, BuildOptions::default()));
		$this->assertSame($expected, (string)$test);
	}

	public function test_hasChildren(): void
	{
		$this->assertTrue((new Comment('a'))->hasChildren());
		$this->assertFalse((new Comment(''))->hasChildren());
	}

	public static function provider_toString_throw(): array
	{
		return [
			['--'],
			['a--b'],
			['a-->b'],
			['--!>'],
		];
	}

	#[DataProvider('provider_toString_throw')]
	public function test_construct_throw(string $value): void
	{
		$this->expectException(ArgumentException::class);
		new Comment($value);
	}
}
