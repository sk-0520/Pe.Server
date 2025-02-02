<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Mvc\Template\Node\Attributes;

abstract class ComponentBase extends NodeBase
{
	/**
	 * 生成
	 * @param Content $content
	 * @param Props $props
	 */
	public function __construct(
		public Content $content,
		public readonly Props $props
	) {
		//NOP
	}

	#region function

	abstract protected function build(): INode;

	#endregion

	#region INode

	public function toString(int $level): string
	{
		//TODO: とりあえずえいやで作るので必要に応じて後で対応する

		$node = $this->build();

		return $node->toString($level);
	}

	#endregion
}
