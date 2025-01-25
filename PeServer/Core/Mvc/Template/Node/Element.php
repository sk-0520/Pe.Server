<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use PeServer\Core\Throws\NotImplementedException;

readonly class Element implements INode
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
	 * @param array<string,string> $attributes
	 * @param INode[]|INode|string $children
	 */
	protected function __construct(
		protected string $tagName,
		protected array $attributes = [],
		array|INode|string $children = []
	) {
		if ($children instanceof INode) {
			$this->children = [$children];
		} else if (is_array($children)) {
			$this->children = $children;
		} else {
			$this->children = [new TextNode($children)];
		}
	}

	#region INode

	public function toString(int $level): string
	{
		throw new NotImplementedException();
	}

	public function __toString(): string
	{
		return $this->toString(0);
	}

	#endregion
}
