<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node;

use PeServer\Core\Mvc\Template\Node\Text\TextBuilder;
use PeServer\Core\Mvc\Template\Node\Text\TextNodeOptions;
use PeServerTest\TestClass;

class TextBuilderTest extends TestClass
{
	public function test_text_normalizes_newlines_and_keeps_raw_text(): void
	{
		$builder = new TextBuilder(new TextNodeOptions("\r\n"));

		$this->assertSame(
			'<b>first</b>&' . "\r\n" . 'second' . "\r\n",
			$builder->build($builder->text("<b>first</b>&\r\nsecond\r"))
		);
	}

	public function test_lines_preserves_empty_lines(): void
	{
		$builder = new TextBuilder(new TextNodeOptions('|'));

		$this->assertSame('first||last', $builder->build($builder->lines(['first', '', 'last'])));
		$this->assertSame('', $builder->build($builder->lines([])));
	}

	public function test_break_repeats_configured_newline(): void
	{
		$builder = new TextBuilder(new TextNodeOptions("\r\n"));

		$this->assertSame("\r\n\r\n\r\n", $builder->build($builder->break(3)));
	}

	public function test_break_with_non_positive_count_is_empty(): void
	{
		$builder = new TextBuilder(new TextNodeOptions("\r\n"));

		$this->assertSame('', $builder->build($builder->break(0)));
		$this->assertSame('', $builder->build($builder->break(-1)));
	}
}
