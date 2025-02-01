<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use PeServer\Core\Collection\Arr;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\NotImplementedException;

class Comment extends NodeBase
{
	/**
	 * 生成。
	 */
	public function __construct(public string $value)
	{
		if(Text::contains($value, "--", false)) {
			throw new ArgumentException('--');
		}
	}

	#region NodeBase

	public function toString(int $level): string
	{
		$lines = Text::splitLines($this->value);
		if (count($lines) === 1) {
			$line = Text::trim($lines[0]);

			if (Text::getByteCount($line) === 0) {
				return "<!-- -->";
			}

			return "<!-- {$line} -->";
		}

		return Text::join(
			PHP_EOL,
			[
				"<!--",
				...Arr::map($lines, fn($a) => Text::trim($a)),
				"-->",
			]
		);
	}

	#endregion
}
