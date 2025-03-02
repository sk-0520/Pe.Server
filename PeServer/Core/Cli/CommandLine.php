<?php

declare(strict_types=1);

namespace PeServer\Core\Cli;

use ArrayAccess;
use Countable;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\CommandLineException;
use PeServer\Core\Throws\InvalidException;
use PeServer\Core\Throws\KeyNotFoundException;
use PeServer\Core\Throws\NotSupportedException;

/**
 * getopt だと知らないオプションで止まるので適当コマンドライン処理。
 */
class CommandLine
{
	#region variable

	/**
	 * @var array<string,LongOptionKey>
	 */
	public array $longOptions;

	#endregion

	/**
	 * 生成。
	 *
	 * @param LongOptionKey[] $longOptions
	 */
	public function __construct(
		array $longOptions
	) {
		$this->longOptions = [];
		foreach ($longOptions as $longOption) {
			if (isset($this->longOptions[$longOption->key])) {
				throw new ArgumentException('$longOptions: ' . $longOption->key);
			}
			$this->longOptions[$longOption->key] = $longOption;
		}

		if (empty($this->longOptions)) {
			throw new ArgumentException('$longOptions: 0');
		}
	}

	#region function

	public function parseArgv(): ParsedResult
	{
		global $argv;
		return $this->parse($argv);
	}

	/**
	 * パース。
	 *
	 * @param non-empty-list<string> $arguments
	 * @return ParsedResult
	 */
	public function parse(array $arguments): ParsedResult
	{
		$length = count($arguments);
		if ($length === 0) { // @phpstan-ignore identical.alwaysFalse
			throw new ArgumentException('$arguments: 0');
		}

		$app = $arguments[0];

		/** @var non-empty-string[] */
		$switches = [];
		/** @var array<non-empty-string,string> */
		$keyValues = [];

		for ($i = 1; $i < $length; $i++) {
			$argument = $arguments[$i];

			if (Text::startsWith($argument, "--", false)) {
				$longOptionKey = Text::substring($argument, 2);
				if (isset($this->longOptions[$longOptionKey])) {
					$longOption = $this->longOptions[$longOptionKey];

					if ($longOption->kind === ParameterKind::Switch) {
						$switches[] = $longOption->key;
						continue;
					}

					$nextIndex = $i + 1;
					if (isset($arguments[$nextIndex])) {
						$nextValue = $arguments[$nextIndex];
						$keyValues[$longOption->key] = $nextValue;
						$i += 1;
					} elseif ($longOption->kind === ParameterKind::NeedValue) {
						throw new CommandLineException("need: {$longOption->key}");
					}
				}
			}
		}

		return new ParsedResult($app, $switches, $keyValues);
	}

	#endregion
}
