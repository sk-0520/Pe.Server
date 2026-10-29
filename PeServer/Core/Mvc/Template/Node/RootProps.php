<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use PeServer\Core\Environment;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\Store\Stores;
use PeServer\Core\Mvc\Template\Node\NodeContext;

/**
 * ページコンポーネントへ渡す共通 Props。
 *
 * @template TNodeContext of NodeContext
 */
readonly class RootProps extends Props
{
	public function __construct(
		public TemplateParameter $parameter,
		/** @var TNodeContext */
		public NodeContext $context
	) {
		//NOP
	}
}
