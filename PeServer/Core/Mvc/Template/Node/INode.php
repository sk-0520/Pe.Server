<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use Stringable;

interface INode extends Stringable
{
	#region function

	/**
	 *
	 * @param non-negative-int $level
	 * @param BuildOptions $buildOptions
	 * @return string
	 */
	public function toString(int $level, BuildOptions $buildOptions): string;

	#endregion
}
