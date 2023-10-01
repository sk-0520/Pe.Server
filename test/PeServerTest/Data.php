<?php

declare(strict_types=1);

namespace PeServerTest;

/**
 * データ。
 *
 * @template TExpected
 */
class Data
{
	/** @phpstan-var TExpected */
	public $expected;
	public $args;
	public $trace;

	public function __construct($expected, ...$args)
	{
		$this->expected = $expected;
		$this->args = $args;
		$this->trace = debug_backtrace(1)[0];
	}

	public function str(): string
	{
		return "{$this->trace["file"]}:{$this->trace["line"]} " . $this->__toString();
	}

	public function __toString(): string
	{
		$s = print_r($this->args, true);
		return $s === null ? '' : $s;
	}
}
