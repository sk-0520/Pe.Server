<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Mvc\Template\Node\Attributes;

readonly class Element extends NodeBase
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
	 * @param array<string,mixed> $props
	 */
	protected function __construct(
		protected string $tagName,
		protected Attributes $attributes = new Attributes([]),
		array $children = [],
		protected array $props = []
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
