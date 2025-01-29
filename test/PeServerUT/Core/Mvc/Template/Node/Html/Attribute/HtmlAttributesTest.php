<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node\Html\Attribute;

use PeServerTest\TestClass;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlAttributes;

class HtmlAttributesTest extends TestClass
{
	#region function

	public function test_constructor_empty()
	{
		$attr = new HtmlAttributes([]);
		$this->assertEmpty($attr->map);
	}

	public function test_constructor_simple()
	{
		$attr = new HtmlAttributes([
			"str" => "abc",
			"int" => 123,
			"bool" => true,
			"translate" => true,
			"null" => null,
		]);
		$this->assertArrayContainsValue("abc", "str", $attr->map);
		$this->assertArrayContainsValue("123", "int", $attr->map);
		$this->assertArrayContainsValue("true", "bool", $attr->map);
		$this->assertArrayContainsValue("yes", "translate", $attr->map);
		$this->assertArrayContainsValue(null, "null", $attr->map);
	}

	public function test_constructor_simple_false()
	{
		$attr = new HtmlAttributes([
			"bool" => false,
			"translate" => false,
		]);
		$this->assertArrayContainsValue("false", "bool", $attr->map);
		$this->assertArrayContainsValue("no", "translate", $attr->map);
	}

	public function test_constructor_data()
	{
		$attr = new HtmlAttributes([
			"data" => [
				"name" => "value",
				"int" => 123,
				"bool" => true,
				"translate" => true,
				"null" => null,
			],
		]);
		$this->assertArrayContainsValue("value", "data-name", $attr->map);
		$this->assertArrayContainsValue("123", "data-int", $attr->map);
		$this->assertArrayContainsValue("true", "data-bool", $attr->map);
		$this->assertArrayContainsValue("true", "data-translate", $attr->map);
		$this->assertArrayContainsValue(null, "data-null", $attr->map);
	}

	#endregion
}
