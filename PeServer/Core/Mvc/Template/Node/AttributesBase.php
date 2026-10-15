<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use Iterator;

abstract class AttributesBase
{
	#region function

	/**
	 * キー一覧を取得。
	 *
	 * @return Iterator<string>
	 * @phpstan-return Iterator<non-empty-string>
	 */
	abstract public function getNames(): Iterator;

	/**
	 * 指定属性名に対する属性を生成。
	 *
	 * @param string $name
	 * @phpstan-param non-empty-string $name
	 * @return string
	 */
	abstract public function buildName(string $name): string;

	#endregion
}
