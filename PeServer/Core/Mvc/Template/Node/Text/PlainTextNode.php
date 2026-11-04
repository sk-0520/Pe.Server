<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Text;

use Override;
use PeServer\Core\Mvc\Template\Node\BuildOptions;
use PeServer\Core\Mvc\Template\Node\INode;

class PlainTextNode implements INode
{
	public function __construct(private string $content)
	{
		//NOP
	}

	#region INode

	#[Override]
	public function hasChildren(): bool
	{
		return false;
	}

	#[Override]
	public function build(int $level, BuildOptions $buildOptions): string
	{
		return $this->content;
	}

	#[Override]
	public function __toString(): string
	{
		return $this->build(0, BuildOptions::default());
	}

	#endregion
}
