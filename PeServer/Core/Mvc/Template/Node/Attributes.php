<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use PeServer\Core\Throws\NotImplementedException;

class Attributes
{
	/**
	 * 生成。
	 *
	 * @param array<string,string|null> $map
	 */
	public function __construct(public array $map)
	{
		//NOP
	}
}
