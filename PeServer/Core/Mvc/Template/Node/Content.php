<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;

class Content
{
	/**
	 * 生成。
	 *
	 * @param INode[] $values
	 */
	public function __construct(
		public array $values = [],
	) {
		//NOP
	}
}
