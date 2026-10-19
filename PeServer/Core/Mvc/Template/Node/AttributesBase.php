<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use BackedEnum;
use Iterator;
use PeServer\Core\Text;
use UnitEnum;
use InvalidArgumentException;

abstract class AttributesBase
{
	#region function

	protected function escapeValue(string $value, AttributeValueMark $mark): string
	{
		if (Text::contains($value, $mark->value, false)) {
			return match ($mark) {
				AttributeValueMark::DoubleQuote => str_replace('"', '&quot;', $value),
				AttributeValueMark::SingleQuote => str_replace("'", '&#39;', $value),
			};
		}

		return $value;
	}

	protected function quoteValue(string $value, AttributeValueMark $mark): string
	{
		$work = $this->escapeValue($value, $mark);
		return $mark->value . $work . $mark->value;
	}

	protected function prepareEnum(UnitEnum $enum): string
	{
		if ($enum instanceof BackedEnum && is_string($enum->value)) {
			return $enum->value;
		}

		return $enum->name;
	}

	protected function prepareValue(string|int|float|bool|UnitEnum $value): string
	{
		if (is_string($value)) {
			return $value;
		} elseif (is_int($value) || is_float($value)) {
			return (string)$value;
		} elseif ($value instanceof UnitEnum) {
			return $this->prepareEnum($value);
		} elseif (is_bool($value)) {
			return $value ? 'true' : 'false';
		}

		// @phpstan-ignore deadCode.unreachable
		throw new InvalidArgumentException('Unsupported value type.');
	}

	protected function buildCommon(string $name, string|int|float|bool|UnitEnum $value, AttributeValueMark $mark): string
	{
		$work = $this->prepareValue($value);

		return $name . '=' . $this->quoteValue($work, $mark);
	}

	/**
	 * キー一覧を取得。
	 *
	 * @return Iterator<string>
	 * @phpstan-return Iterator<non-empty-string>
	 */
	abstract public function getNames(): Iterator;

	/**
	 * 指定属性名に対する属性を生成。
	 *
	 * @param string $name
	 * @phpstan-param non-empty-string $name
	 * @return string
	 */
	abstract public function build(string $name, AttributeValueMark $mark = AttributeValueMark::DoubleQuote): string;

	#endregion
}
