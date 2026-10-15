<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use PeServer\Core\Throws\NotImplementedException;

class TextNode extends NodeBase
{
	#region define

	public const ESCAPE_RAW = 0;
	public const ESCAPE_DEFAULT = 1;

	#endregion

	public function __construct(
		public string $content,
		public int $escape = self::ESCAPE_DEFAULT,
	) {
		//NOP
	}

	#region NodeBase

	public function toString(int $level): string
	{
		return match ($this->escape) {
			self::ESCAPE_RAW => $this->content,
			default => htmlspecialchars($this->content, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5),
		};
	}

	#endregion
}
