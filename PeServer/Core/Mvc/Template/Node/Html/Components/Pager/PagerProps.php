<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Components\Pager;

use PeServer\Core\Mvc\Pagination;
use PeServer\Core\Mvc\Template\Node\Props;

readonly class PagerProps extends Props
{
	public function __construct(
		public Pagination $data,
		public string $href,
		public string $jumpHead = '<<',
		public string $jumpTail = '>>',
		public string $jumpPrev = '<',
		public string $jumpNext = '>',
	) {
		//NOP
	}
}
