<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Mvc\Template\Node\Attributes;

class Element extends NodeBase
{
	#region variable

	/**
	 *
	 * @var INode[]
	 */
	protected array $children;

	#endregion

	/**
	 *
	 * @param string $tagName
	 * @param Attributes $attributes
	 * @param INode[] $children
	 * @param object $props
	 */
	protected function __construct(
		protected readonly string $tagName,
		protected Attributes $attributes,
		array $children,
		protected readonly object $props,
		protected readonly ElementOptions $options
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
