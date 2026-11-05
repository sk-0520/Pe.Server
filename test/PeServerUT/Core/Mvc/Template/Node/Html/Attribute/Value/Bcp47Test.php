<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node\Html\Attribute\Value;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Bcp47;
use PeServerTest\TestClass;

class Bcp47Test extends TestClass
{
	public function test_values(): void
	{
		self::assertSame('ja', Bcp47::Japanese->value);
		self::assertSame('en-US', Bcp47::EnglishUnitedStates->value);
		self::assertSame('zh-Hant', Bcp47::ChineseTraditional->value);
		self::assertSame('pt-BR', Bcp47::PortugueseBrazil->value);
		self::assertSame('und', Bcp47::Undetermined->value);
	}
}
