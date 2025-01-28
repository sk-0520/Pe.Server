<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Mvc\Template\Node\Attributes;

class Element extends NodeBase
{
	/**
	 *
	 * @param non-empty-string $tagName
	 * @param Attributes $attributes
	 * @param INode[] $children
	 * @param Props $props
	 * @param ElementOptions $options
	 */
	public function __construct(
		public readonly string $tagName,
		public Attributes $attributes,
		public array $children,
		public readonly Props $props,
		public readonly ElementOptions $options
	) {
		$this->children = $children;
	}

	#region INode

	public function toString(int $level): string
	{
		throw new NotImplementedException();
	}

	#endregion
}
