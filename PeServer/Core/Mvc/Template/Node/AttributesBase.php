<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use Iterator;
use PeServer\Core\Text;

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

	protected function toValue(string $value, AttributeValueMark $mark = AttributeValueMark::DoubleQuote): string
	{
		$work = $this->escapeValue($value, $mark);
		return $mark->value . $work . $mark->value;
	}

	protected function buildCommon(string $name, string|int|float|bool $value): string
	{
		$work = $value;

		if (is_int($work)) {
			$work = (string)$work;
		} elseif (is_float($work)) {
			$work = (string)$work;
		} elseif (is_bool($work)) {
			$work = $work ? 'true' : 'false';
		}

		return $name . '=' . $this->toValue($work);
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
	abstract public function build(string $name): string;

	#endregion
}
