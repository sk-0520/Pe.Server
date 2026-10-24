<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

abstract class NodeBase implements INode
{
	#region INode

	abstract public function toString(int $level, NodeOptions $options): string;

	public function __toString(): string
	{
		return $this->toString(0, NodeOptions::default());
	}

	#endregion
}
