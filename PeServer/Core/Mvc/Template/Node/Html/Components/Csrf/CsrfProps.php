<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Components\Csrf;

use PeServer\Core\Mvc\Template\Node\Props;

readonly class CsrfProps extends Props
{
	public function __construct(
		/** @var 'name'|'id' */
		public string $type = 'name',
	) {
		//NOP
	}
}
