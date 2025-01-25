<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use Stringable;

interface INode extends Stringable
{
	#region function

	public function toString(int $level): string;

	#endregion
}
