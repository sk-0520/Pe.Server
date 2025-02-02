<?php

declare(strict_types=1);

namespace PeServer\Core\Cli;

use ArrayAccess;
use Countable;
use PeServer\Core\Collection\Arr;
use PeServer\Core\Throws\InvalidException;
use PeServer\Core\Throws\KeyNotFoundException;
use PeServer\Core\Throws\NotSupportedException;

/**
 */
class CliOptions
{
	#region variable

	/**
	 * @param array<string,string|string[]|bool> $values
	 * @phpstan-ignore missingType.iterableValue
	 */
	public array $data;

	#endregion

	/**
	 * 生成。
	 *
	 * @param LongOptionKey[] $longOptions
	 */
	public function __construct(
		public array $longOptions
	) {
		$opts = getopt("", Arr::map($longOptions, fn($a) => $a->key . match ($a->kind) {
			ParameterKind::NeedValue => ':',
			ParameterKind::OptionValue => '::',
			ParameterKind::KeyOnly => '',
		}));

		if ($opts === false) {
			throw new InvalidException();
		}

		$this->data = [];
		foreach ($opts as $key => $value) {
			if (is_bool($value)) {
				$this->data[$key] = $value;
			} else if(is_string($value)) {
				$this->data[$key] = $value;
			} else {
				$this->data[$key] = $value;
			}
		}
	}
}
