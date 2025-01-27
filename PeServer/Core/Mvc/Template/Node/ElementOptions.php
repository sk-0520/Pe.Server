<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Mvc\Template\Node\Attributes;

class ElementOptions
{
	/**
	 * 生成。
	 *
	 * @param bool $isInline インライン要素か。
	 * @param bool $selfClosing 自己終了タグか。
	 */
	protected function __construct(
		public bool $isInline,
		public bool $selfClosing
	) {
		//NOP
	}
}
