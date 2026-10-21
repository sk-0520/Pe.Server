<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Attribute\Value;

use PeServer\Core\Text;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Mvc\Template\Node\Attribute\Value\IAttributeValue;

/**
 * @template TValue of string|int|float|object
 */
readonly abstract class ValuesBase implements IAttributeValue
{
	#region variable

	/**
	 * @var list<TValue>
	 */
	private array $values;

	#endregion

	/**
	 * 生成。
	 *
	 * @phpstan-param TValue|list<TValue> $value
	 */
	public function __construct(string|int|float|object|array $value)
	{
		$this->values = is_array($value) ? $value : [$value];
	}

	#region function

	/**
	 * @param TValue $value
	 * @return string
	 */
	protected function convertValue(string|int|float|object $value): string
	{
		// @phpstan-ignore cast.string
		return (string)$value;
	}

	#endregion

	#region IAttributeValue

	public function toValue(): string
	{
		$results = Arr::map($this->values, fn($a) => $this->convertValue($a));
		return Text::join(' ', $results);
	}

	#endregion
}
