<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Attribute;

use BackedEnum;
use Iterator;
use PeServer\Core\Text;
use UnitEnum;
use InvalidArgumentException;
use PeServer\Core\Collections\Arr;
use ReflectionProperty;
use PeServer\Core\Mvc\Template\Node\Attribute\AttributeValueMark;
use PeServer\Core\Mvc\Template\Node\Attribute\Value\IAttributeValue;
use PeServer\Core\Mvc\Template\Node\Attribute\BuildName;

abstract class AttributesBase
{
	#region function

	protected function escapeValue(string $value, AttributeValueMark $mark): string
	{
		$work = strtr($value, ['&' => '&amp;', '<' => '&lt;', '>' => '&gt;']);

		return match ($mark) {
			AttributeValueMark::DoubleQuote => str_replace('"', '&quot;', $work),
			AttributeValueMark::SingleQuote => str_replace("'", '&#39;', $work),
		};
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

	protected function prepareValue(string $name, string|int|float|bool|UnitEnum|IAttributeValue $value): string
	{
		if (is_string($value)) {
			return $value;
		} elseif (is_int($value) || is_float($value)) {
			return (string)$value;
		} elseif ($value instanceof UnitEnum) {
			return $this->prepareEnum($value);
		} elseif ($value instanceof IAttributeValue) {
			return $value->toValue();
		} elseif (is_bool($value)) {
			if (property_exists($this, $name)) {
				$property = new ReflectionProperty($this, $name);
				$attributes = $property->getAttributes(BuildAttributeBool::class);
				if (!Arr::isNullOrEmpty($attributes)) {
					$instance = $attributes[0]->newInstance();
					return $value ? $instance->true : $instance->false;
				}
			}

			return $value ? 'true' : 'false';
		}

		// @phpstan-ignore deadCode.unreachable
		throw new InvalidArgumentException('Unsupported value type.');
	}

	protected function buildNameByReflection(ReflectionProperty $property): string|null
	{
		$attributes = $property->getAttributes(BuildName::class);
		if (!Arr::isNullOrEmpty($attributes)) {
			$instance = $attributes[0]->newInstance();
			return $instance->name;
		}

		return null;
	}

	protected function buildName(string $name): string
	{
		if (property_exists($this, $name)) {
			$result = $this->buildNameByReflection(new ReflectionProperty($this, $name));
			if ($result !== null) {
				return $result;
			}
		}

		return $name;
	}


	protected function buildCommon(string $name, string|int|float|bool|UnitEnum|IAttributeValue $value, AttributeValueMark $mark): string
	{
		$workName = $this->buildName($name);
		$workValue = $this->prepareValue($name, $value);

		return $workName . '=' . $this->quoteValue($workValue, $mark);
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
