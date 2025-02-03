<?php

declare(strict_types=1);

namespace PeServer\Core\Cli;

use ArrayAccess;
use PeServer\Core\Collection\Arr;
use PeServer\Core\Throws\KeyNotFoundException;
use PeServer\Core\Throws\NotImplementedException;
use TypeError;

/**
 *
 * @implements ArrayAccess<string,string|bool>
 */
readonly class ParsedResult implements ArrayAccess
{
	/**
	 *
	 * @param string $command
	 * @param non-empty-string[] $switches
	 * @param array<non-empty-string,string> $keyValues
	 */
	public function __construct(
		public string $command,
		public array $switches,
		public array $keyValues,
	) {
		//NOP
	}

	#region function

	public function getValue(string $key): string
	{
		if (!isset($this->keyValues[$key])) {
			throw new KeyNotFoundException($key);
		}
		return $this->keyValues[$key];
	}

	public function hasSwitch(string $key): bool
	{
		return Arr::in($this->switches, $key);
	}

	#endregion

	#region ArrayAccess

	/**
	 *
	 * @param string $offset
	 * @return bool
	 * @throws TypeError
	 */
	public function offsetExists(mixed $offset): bool
	{
		// @phpstan-ignore function.alreadyNarrowedType
		if (!is_string($offset)) {
			throw new TypeError();
		}

		if (isset($this->keyValues[$offset])) {
			return true;
		}

		return Arr::in($this->switches, $offset);
	}

	/**
	 *
	 * @param string $offset
	 * @return string|bool
	 * @throws TypeError
	 */
	public function offsetGet(mixed $offset): mixed
	{
		// @phpstan-ignore function.alreadyNarrowedType
		if (!is_string($offset)) {
			throw new TypeError();
		}

		if (isset($this->keyValues[$offset])) {
			return $this->keyValues[$offset];
		}

		return Arr::in($this->switches, $offset);
	}
	public function offsetSet(mixed $offset, mixed $value): void
	{
		throw new NotImplementedException();
	}
	public function offsetUnset(mixed $offset): void
	{
		throw new NotImplementedException();
	}

	#endregion
}
