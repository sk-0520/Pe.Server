<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use Override;

class Fragment extends NodeBase
{
	#region variable

	/**
	 * @var INode[]
	 */
	private array $nodes;

	#endregion

	/**
	 * 生成。
	 *
	 * @param INode[] $nodes
	 */
	public function __construct(
		INode|array $nodes
	) {
		if ($nodes instanceof INode) {
			$this->nodes = [$nodes];
		} else {
			$this->nodes = $nodes;
		}
	}

	#region NodeBase

	#[Override]
	public function hasChildren(): bool
	{
		return !empty($this->nodes);
	}

	#[Override]
	public function toString(int $level, BuildOptions $buildOptions): string
	{
		$result = '';
		foreach ($this->nodes as $node) {
			$result .= $node->toString($level, $buildOptions);
		}
		return $result;
	}

	#endregion
}
