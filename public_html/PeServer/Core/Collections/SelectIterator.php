<?php

declare(strict_types=1);

namespace PeServer\Core\Collections;

use \Iterator;
use \IteratorIterator;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\CallbackTypeError;

/**
 * select イテレータ。
 *
 * @template TKey of array-key
 * @template TValue
 * @template TResult
 * @extends IteratorIterator<TKey,TResult>
 */
class SelectIterator extends IteratorIterator //@phpstan-ignore-line Generic
{
	/**
	 * 生成。
	 *
	 * @param Iterator $iterator
	 * @phpstan-param Iterator<TKey,TValue> $iterator
	 * @param mixed $callback
	 * @phpstan-param callable(TValue,TKey):(TResult) $callback
	 */
	public function __construct(
		Iterator $iterator,
		private mixed $callback
	) {
		if (!is_callable($callback)) { //@phpstan-ignore-line phpstan-param callable
			throw new CallbackTypeError('$callback');
		}
		parent::__construct($iterator);
	}

	#region IteratorIterator

	/**
	 * @phpstan-return TResult
	 */
	public function current(): mixed
	{
		return call_user_func($this->callback, parent::current(), parent::key());
	}

	#endregion
}
