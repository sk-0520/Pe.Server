<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use ArrayAccess;
use Iterator;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Text;
use Stringable;
use Override;
use PeServer\Core\Mvc\Template\Node\Attribute\AttributesBase;
use PeServer\Core\Mvc\Template\Node\Attribute\AttributeValueMark;

/**
 * data-* 属性。
 *
 * @implements ArrayAccess<non-empty-string,string>
 */
class DataAttributes extends AttributesBase implements Stringable, ArrayAccess
{
	public function __construct(
		/**
		 * @var array<string,string>
		 * @phpstan-var array<non-empty-string,string>
		 */
		private array $attributes = []
	) {
		//NOP
	}

	#region function

	public function isEmpty(): bool
	{
		return Arr::isNullOrEmpty($this->attributes);
	}

	#endregion

	#region AttributesBase

	#[Override]
	public function getNames(): Iterator
	{
		// non-empty-string でやんす
		// @phpstan-ignore generator.valueType
		yield from Arr::getKeys($this->attributes);
	}

	#[Override]
	public function build(string $name, AttributeValueMark $mark = AttributeValueMark::DoubleQuote): string
	{
		$attr = $this->attributes[$name];
		return 'data-' . $name . '=' . $this->quoteValue($attr, $mark);
	}


	#endregion

	#region ArrayAccess

	public function offsetExists(mixed $offset): bool
	{
		return isset($this->attributes[$offset]);
	}

	public function offsetGet(mixed $offset): string
	{
		return $this->attributes[$offset];
	}

	/**
	 *
	 * @phpstan-param non-empty-string|mixed $offset
	 */
	public function offsetSet(mixed $offset, mixed $value): void
	{
		if ($offset === null) {
			throw new \InvalidArgumentException('Offset cannot be null.');
		} else {
			$this->attributes[$offset] = $value;
		}
	}

	public function offsetUnset(mixed $offset): void
	{
		unset($this->attributes[$offset]);
	}

	#endregion

	#region Stringable

	public function __toString(): string
	{
		$parts = [];
		foreach ($this->attributes as $key => $value) {
			$parts[] = 'data-' . $key . '="' . $value . '"';
		}

		return Text::join('', $parts);
	}

	#endregion
}
