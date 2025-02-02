<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Mvc\Template\Node\Attributes;
use PeServer\Core\Mvc\Template\Node\Html\Tag;

/**
 *
 *
 */
abstract class ComponentBase extends NodeBase
{
	/**
	 * 生成
	 * @template TContent of Content
	 * @template TProps of Props
	 * @param Content $content
	 * @phpstan-param TContent $content
	 * @param Props $props
	 * @phpstan-param TProps $props
	 * @param Tag $tag
	 */
	public function __construct(
		public Content $content,
		public readonly Props $props,
		public readonly Tag $tag
	) {
		//NOP
	}

	#region function

	abstract protected function build(): INode;

	/**
	 * 子コンポーネントの簡易生成。
	 * @template TComponent of ComponentBase
	 * @template TProps of Props
	 * @param class-string<ComponentBase> $component
	 * @phpstan-param class-string<TComponent> $component
	 * @param Props $props
	 * @phpstan-param TProps $props
	 * @return ComponentBase
	 * @phpstan-return TComponent
	 */
	protected function create(string $component, Props $props): self
	{
		return new $component($props, $this->tag);
	}

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
