<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

interface IControllerFactory
{
	/**
	 * コントローラ生成処理。
	 *
	 * @template TArguments
	 * @param string $controllerClassName コントローラクラス名。
	 * @phpstan-param class-string<ControllerBase> $controllerClassName
	 * @param mixed $arguments 生成時に渡される追加パラメータ。
	 * @phpstan-param TArguments|null $arguments
	 */
	function new(string $controllerClassName, mixed $arguments = null): ControllerBase;
}
