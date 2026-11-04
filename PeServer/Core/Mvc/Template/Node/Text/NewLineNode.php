<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Text;

use Override;
use PeServer\Core\Mvc\Template\Node\BuildOptions;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\TextNode;

class NewLineNode extends PlainTextNode
{
	public function __construct(string $newline)
	{
		parent::__construct($newline);
	}
}
