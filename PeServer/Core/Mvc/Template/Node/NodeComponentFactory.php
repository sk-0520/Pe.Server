<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use PeServer\Core\DI\DiFactoryTrait;
use PeServer\Core\DI\DiFactoryBase;
use PeServer\Core\Mvc\Template\Node\Attribute\AttributesBase;
use PeServer\Core\Text;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\ComponentBase;

class NodeComponentFactory extends DiFactoryBase
{
	use DiFactoryTrait;

	/**
	 * ノードテンプレートを実体化。
	 *
	 * @template TProps of \PeServer\Core\Mvc\Template\Node\Props
	 * @template TComponent of \PeServer\Core\Mvc\Template\Node\ComponentBase<TProps>
	 * @param string $component
	 * @phpstan-param class-string<TComponent> $component
	 * @return ComponentBase
	 * @phpstan-return TComponent
	 */
	public function create(string $component): ComponentBase
	{
		throw new NotImplementedException();
	}
}
