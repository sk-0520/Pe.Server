<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use Override;

/**
 * @template TProps of Props
 */
abstract class ComponentBase extends Element
{
	/**
	 * @phpstan-param TProps $props
	 */
	protected function __construct(public readonly Props $props)
	{
		//NOP
	}

	#region function

	abstract protected function getNode(): INode;

	#endregion

	#region Element

	#[Override]
	public function toString(int $level, BuildOptions $buildOptions): string
	{
		return $this->getNode()->toString($level, $buildOptions);
	}

	#endregion
}
