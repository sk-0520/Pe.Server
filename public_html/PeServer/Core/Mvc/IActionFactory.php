<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

interface IActionFactory
{
	/**
	 * アクション生成処理。
	 *
	 * @template TArguments
	 * @param string $actionClassName アクションクラス名。
	 * @phpstan-param class-string<Action> $actionClassName
	 * @param mixed $arguments 生成時に渡される追加パラメータ。
	 * @phpstan-param TArguments|null $arguments
	 */
	function new(string $actionClassName, mixed $arguments = null): Action;
}
