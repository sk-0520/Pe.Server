<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use PeServer\Core\Environment;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\Store\Stores;

/**
 * ページコンポーネントへ渡す共通 Props。
 *
 * @template TTemplateParameter of TemplateParameter
 */
readonly class RootProps extends Props
{
	public function __construct(
		/** @param TTemplateParameter $parameter */
		public TemplateParameter $parameter,
		public Stores $stores,
		public Environment $environment
	) {
		//NOP
	}
}
