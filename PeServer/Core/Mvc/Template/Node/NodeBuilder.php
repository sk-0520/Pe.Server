<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use PeServer\Core\Mvc\Template\Node\Attribute\AttributesBase;
use PeServer\Core\Text;
use PeServer\Core\Throws\NotImplementedException;

class NodeBuilder
{
	#region function

	public function build(INode $node, ?BuildOptions $buildOptions = null): string
	{
		return $node->toString(0, $buildOptions ?? BuildOptions::default());
	}

	#endregion
}
