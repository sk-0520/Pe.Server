<?php

declare(strict_types=1);

namespace PeServer\Core\Collections;

use Generator;
use Iterator;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\CallbackTypeError;

/**
 * ジェネレータ イテレータ。
 *
 * @template TKey of array-key
 * @template TValue
 * @implements Iterator<TKey,TValue>
 */
class GeneratorIterator implements Iterator
{
	#region variable

	private Generator $generator;

	#endregion

	/**
	 * 生成。
	 *
	 * @param mixed $factory
	 * @phpstan-param callable():Generator<TKey,TValue> $factory
	 */
	public function __construct(
		private mixed $factory
	) {
		if (!is_callable($factory)) { //@phpstan-ignore-line phpstan-param callable
			throw new CallbackTypeError('$factory');
		}
		$this->generator = call_user_func($this->factory);
	}

	#region Iterator

	public function rewind(): void
	{
		$this->generator = call_user_func($this->factory);
	}

	public function key(): mixed
	{
		return $this->generator->key();
	}

	public function current(): mixed
	{
		return $this->generator->current();
	}

	public function next(): void
	{
		$this->generator->next();
	}

	public function valid(): bool
	{
		return $this->generator->valid();
	}

	#endregion
}
