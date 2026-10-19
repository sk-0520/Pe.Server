<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node;

use Iterator;
use Override;
use PeServer\Core\Mvc\Template\Node\AttributesBase;
use PeServer\Core\Mvc\Template\Node\AttributeValueMark;
use PeServer\Core\Throws\NotImplementedException;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;
use UnitEnum;

class AttributesBaseTest extends TestClass
{

	public static function provider_escapeValue()
	{
		return [
			["", "", AttributeValueMark::DoubleQuote],
			["", "", AttributeValueMark::SingleQuote],
			["abc", "abc", AttributeValueMark::DoubleQuote],
			["abc", "abc", AttributeValueMark::SingleQuote],
			["a&quot;b&quot;c", "a\"b\"c", AttributeValueMark::DoubleQuote],
			["a&#39;b&#39;c", "a'b'c", AttributeValueMark::SingleQuote],
			["a'b'c", "a'b'c", AttributeValueMark::DoubleQuote],
			["a\"b\"c", "a\"b\"c", AttributeValueMark::SingleQuote],
		];
	}

	#[DataProvider('provider_escapeValue')]
	public function test_escapeValue(string $expected, string $value, AttributeValueMark $mark)
	{
		$test = new LocalAttributes();
		$actual = $test->local_escapeValue($value, $mark);
		$this->assertSame($expected, $actual);
	}

	public static function provider_quote()
	{
		return [
			["\"\"", "", AttributeValueMark::DoubleQuote],
			["''", "", AttributeValueMark::SingleQuote],
			["\"abc\"", "abc", AttributeValueMark::DoubleQuote],
			["'abc'", "abc", AttributeValueMark::SingleQuote],
			["\"a&quot;b&quot;c\"", "a\"b\"c", AttributeValueMark::DoubleQuote],
			["'a&#39;b&#39;c'", "a'b'c", AttributeValueMark::SingleQuote],
			["\"a'b'c\"", "a'b'c", AttributeValueMark::DoubleQuote],
			["'a\"b\"c'", "a\"b\"c", AttributeValueMark::SingleQuote],
		];
	}

	#[DataProvider('provider_quote')]
	public function test_quote(string $expected, string $value, AttributeValueMark $mark)
	{
		$test = new LocalAttributes();
		$actual = $test->local_quoteValue($value, $mark);
		$this->assertSame($expected, $actual);
	}

	public static function provider_prepareValue()
	{
		return [
			["123", 123],
			["123.45", 123.45],
			["true", true],
			["false", false],
			["abc", "abc"],
			["A", LocalEnumUnit::A],
			["B", LocalEnumUnit::B],
			["aaa", LocalEnumBackend::A],
			["bbb", LocalEnumBackend::B],
		];
	}

	#[DataProvider('provider_prepareValue')]
	public function test_prepareValue(string $expected, string|int|float|bool|UnitEnum $value)
	{
		$test = new LocalAttributes();
		$actual = $test->local_prepareValue($value);
		$this->assertSame($expected, $actual);
	}

	public static function provider_buildCommon()
	{
		return [
			["name=\"value\"", "name", "value", AttributeValueMark::DoubleQuote],
			["name='value'", "name", "value", AttributeValueMark::SingleQuote],
		];
	}

	#[DataProvider('provider_buildCommon')]
	public function test_buildCommon(string $expected, string $name, string|int|float|bool|UnitEnum $value, AttributeValueMark $mark)
	{
		$test = new LocalAttributes();
		$actual = $test->local_buildCommon($name, $value, $mark);
		$this->assertSame($expected, $actual);
	}
}

final class LocalAttributes extends AttributesBase
{
	#region function

	public function local_escapeValue(string $value, AttributeValueMark $mark): string
	{
		return $this->escapeValue($value, $mark);
	}

	public function local_quoteValue(string $value, AttributeValueMark $mark = AttributeValueMark::DoubleQuote): string
	{
		return $this->quoteValue($value, $mark);
	}

	public function local_prepareValue(string|int|float|bool|UnitEnum $value): string
	{
		return $this->prepareValue($value);
	}

	public function local_buildCommon(string $name, string|int|float|bool|UnitEnum $value, AttributeValueMark $mark = AttributeValueMark::DoubleQuote): string
	{
		return $this->buildCommon($name, $value, $mark);
	}

	#endregion

	#region AttributesBase

	#[Override]
	public function getNames(): Iterator
	{
		throw new NotImplementedException();
	}

	#[Override]
	public function build(string $name, AttributeValueMark $mark = AttributeValueMark::DoubleQuote): string
	{
		throw new NotImplementedException();
	}

	#endregion
}

enum LocalEnumUnit
{
	case A;
	case B;
}

enum LocalEnumBackend: string
{
	case A = 'aaa';
	case B = 'bbb';
}
