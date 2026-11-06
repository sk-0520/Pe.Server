<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use Override;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;

class Comment extends NodeBase
{
	/**
	 * 生成。
	 */
	public function __construct(readonly public string $value)
	{
		//NOP
	}

	#region function

	protected function escapeComment(string $value): string
	{
		if (Text::contains($value, "--", false)) {
			// コメントとして - 連続が許容されないので − 連続に差し替える
			return Text::replace($value, "--", "−−");
		}

		return $value;
	}

	#endregion

	#region NodeBase

	#[Override]
	public function hasChildren(): bool
	{
		return !Text::isNullOrEmpty($this->value);
	}

	#[Override]
	public function build(int $level, BuildOptions $buildOptions): string
	{
		$lines = Text::splitLines($this->value);
		if (count($lines) === 1) {
			$line = Text::trim($lines[0]);

			if (Text::getByteCount($line) === 0) {
				return "<!-- -->";
			}

			return "<!-- {$this->escapeComment($line)} -->";
		}

		return Text::join(
			PHP_EOL,
			[
				"<!--",
				...Arr::map($lines, fn($a) => $this->escapeComment(Text::trim($a))),
				"-->",
			]
		);
	}

	#endregion
}
