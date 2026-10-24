<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Mvc\Template\Node\AttributesBase;

class ElementOptions
{
	/**
	 * 生成。
	 *
	 * @param bool $isInline インライン要素か。現状なんも考慮しない。
	 * @param bool $selfClosing 自己終了タグか。
	 * @param bool $rawText 子のテキストをエスケープせず出力するか(script/style 用)。
	 */
	public function __construct(
		public bool $isInline,
		public bool $selfClosing,
		public bool $rawText = false
	) {
		//NOP
	}
}
