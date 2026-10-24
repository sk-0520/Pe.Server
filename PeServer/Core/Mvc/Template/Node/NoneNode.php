<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use Override;

final class NoneNode extends NodeBase
{
	#region variable

	private static NoneNode|null $instance = null;

	#endregion

	#region function

	public static function get(): INode
	{
		return self::$instance ??= new NoneNode();
	}

	#endregion

	#region NodeBase

	#[Override]
	public function hasChildren(): bool
	{
		return false;
	}

	#[Override]
	public function toString(int $level, BuildOptions $buildOptions): string
	{
		return '';
	}

	#endregion
}
