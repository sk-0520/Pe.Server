<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use Override;
use PeServer\Core\Mvc\Template\Node\BuildOptions;

/**
 * @template TProps of Props
 */
abstract class ComponentBase extends NodeBase
{
	#region variable

	private INode|null $node = null;

	#endregion

	/**
	 * @phpstan-param TProps $props
	 */
	protected function __construct(public readonly Props $props)
	{
		//NOP
	}

	#region function

	/**
	 * コンポーネントが表現するノードを生成する。
	 *
	 * 結果は初回に 1 回だけ生成してキャッシュされるため、props のみから決定される純粋な処理にすること。
	 */
	abstract protected function getNode(): INode;

	private function getCachedNode(): INode
	{
		return $this->node ??= $this->getNode();
	}

	#endregion

	#region NodeBase

	#[Override]
	public function hasChildren(): bool
	{
		return $this->getCachedNode()->hasChildren();
	}

	#[Override]
	public function toString(int $level, BuildOptions $buildOptions): string
	{
		return $this->getCachedNode()->toString($level, $buildOptions);
	}

	#endregion
}
