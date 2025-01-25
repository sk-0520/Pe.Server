<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use PeServer\Core\Throws\NotImplementedException;

final readonly class TextNode implements INode
{
	#region define

	public const ESCAPE_DEFAULT = 1;

	#endregion

	public function __construct(
		public string $content,
		public int $escape = self::ESCAPE_DEFAULT,
	) {
		//NOP
	}

	#region INode

	public function toString(int $level): string
	{
		return $this->content;
	}

	public function __toString(): string
	{
		return $this->toString(0);
	}

	#endregion
}
