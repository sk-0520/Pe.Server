<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node;

use PeServer\Core\Mvc\Template\Node\Attributes;
use PeServer\Core\Mvc\Template\Node\Element;
use PeServer\Core\Mvc\Template\Node\ElementOptions;
use PeServer\Core\Mvc\Template\Node\Props;
use PeServerTest\TestClass;

class ElementTest extends TestClass
{
	public function test_constructor()
	{
		$actual = new Element("name", new Attributes([]), [], new Props(), new ElementOptions(false, false));
		$this->assertSame("name", $actual->tagName);
	}
}
