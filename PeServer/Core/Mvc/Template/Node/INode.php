<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use Stringable;

interface INode extends Stringable
{
	#region function

	/**
	 * 子ノードが存在するか。
	 */
	public function hasChildren(): bool;

	/**
	 * 文字列化。
	 *
	 * @param non-negative-int $level インデントレベル。
	 * @param BuildOptions $buildOptions ビルドオプション。
	 * @return string
	 */
	public function build(int $level, BuildOptions $buildOptions): string;

	#endregion
}
