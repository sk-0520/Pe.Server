<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

final class TextContent extends Content
{
	/**
	 *
	 * @param string $value
	 *
	 */
	public function __construct(
		public string $value = "",
	) {
		//NOP
	}
}
