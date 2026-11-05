<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use Override;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Text;
use PeServer\Core\Throws\NotImplementedException;

class TextNode extends NodeBase
{
	#region define

	public const ESCAPE_DEFAULT = 0;
	public const ESCAPE_RAW = 1;

	#endregion

	public function __construct(
		public readonly string $content,
		public readonly int $escape = self::ESCAPE_DEFAULT,
	) {
		//NOP
	}

	#region NodeBase

	#[Override]
	public function hasChildren(): bool
	{
		return !Text::isNullOrEmpty($this->content);
	}


	#[Override]
	public function build(int $level, BuildOptions $buildOptions): string
	{
		return match ($this->escape) {
			self::ESCAPE_RAW => $this->content,
			default => htmlspecialchars($this->content, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5),
		};
	}

	#endregion
}
