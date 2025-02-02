<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Component\Layout;

use PeServer\Core\Mvc\Template\Node\ComponentBase;
use PeServer\Core\Mvc\Template\Node\Content;
use PeServer\Core\Mvc\Template\Node\Html\Tag;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Throws\NotImplementedException;

readonly class LayoutProps extends Props
{
	public function __construct(
		public string $language,
	) {
		//NOP
	}
}
