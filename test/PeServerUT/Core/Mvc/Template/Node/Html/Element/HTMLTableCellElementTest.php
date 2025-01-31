<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node\Html\Attribute;

use PeServerTest\TestClass;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableDataCellAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableHeaderCellAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Element\HTMLTableCellElement;

class HTMLTableCellElementTest extends TestClass
{
	#region function

	public function test_td_null_attr()
	{
		$element = new HTMLTableCellElement("td", attributes: null);
		$this->assertInstanceOf(HTMLTableDataCellAttributes::class, $element->attributes);
	}

	public function test_th_null_attr()
	{
		$element = new HTMLTableCellElement("th", attributes: null);
		$this->assertInstanceOf(HTMLTableHeaderCellAttributes::class, $element->attributes);
	}

	#endregion
}
