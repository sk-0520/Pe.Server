<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

class Content
{
	#region variable

	/**
	 * @var INode[]
	 */
	public array $nodes;

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
}
