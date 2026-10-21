<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
readonly class BuildAttributeNameOnly
{
	public function __construct(
		/**
		 * 値が指定値の場合に属性名のみを出力する
		 */
		public bool $condition = true
	) {
		//NOP
	}
}
