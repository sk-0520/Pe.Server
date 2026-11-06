<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node;

use PeServer\Core\Mvc\Template\Node\BuildOptions;
use PeServer\Core\Mvc\Template\Node\Comment;
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
			['<!-- −− -->', '--'],
			['<!-- a−−b -->', 'a--b'],
			['<!-- a−−>b -->', 'a-->b'],
			['<!-- −−!> -->', '--!>'],
			['<!--' . PHP_EOL . 'a' . PHP_EOL . 'b' . PHP_EOL . '-->', 'a' . PHP_EOL . 'b'],
			['<!--' . PHP_EOL . 'a' . PHP_EOL . 'b' . PHP_EOL . '-->', '  a  ' . PHP_EOL . '  b  '],
		];
	}

	#[DataProvider('provider_toString')]
	public function test_build(string $expected, string $value): void
	{
		$test = new Comment($value);
		$this->assertSame($expected, $test->build(0, BuildOptions::default()));
		$this->assertSame($expected, (string)$test);
	}

	public function test_hasChildren(): void
	{
		$this->assertTrue((new Comment('a'))->hasChildren());
		$this->assertFalse((new Comment(''))->hasChildren());
	}
}
