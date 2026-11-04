<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node;

use PeServer\Core\Mvc\Template\Node\BuildOptions;
use PeServer\Core\Mvc\Template\Node\ICustomBuildOptions;
use PeServerTest\TestClass;

class BuildOptionsTest extends TestClass
{
	public function test_default(): void
	{
		$test = BuildOptions::default();
		$this->assertSame(BuildOptions::FORMAT_NEWLINE | BuildOptions::FORMAT_INDENT, $test->format);
		$this->assertSame("\t", $test->indent);
		$this->assertInstanceOf(ICustomBuildOptions::class, $test);
	}
}
