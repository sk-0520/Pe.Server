<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use Override;
use PeServer\Core\Mvc\Template\Node\BuildOptions;

abstract class NodeBase implements INode
{
	#region INode

	#[Override]
	abstract public function hasChildren(): bool;

	#[Override]
	abstract public function build(int $level, BuildOptions $buildOptions): string;

	public function __toString(): string
	{
		return $this->build(0, BuildOptions::default());
	}

	#endregion
}
