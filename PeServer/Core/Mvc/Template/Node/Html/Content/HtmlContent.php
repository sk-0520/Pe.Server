<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Content;

use PeServer\Core\Mvc\Template\Node\Content;
use PeServer\Core\Mvc\Template\Node\INode;

class HtmlContent extends Content
{
	/**
	 * 生成。
	 *
	 * @param INode[] $values
	 */
	public function __construct(
		array $values = [],
	) {
		parent::__construct($values);
	}
}
