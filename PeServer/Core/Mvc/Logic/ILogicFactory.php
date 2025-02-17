<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Logic;

use PeServer\Core\Mvc\Logic\LogicParameter;

interface ILogicFactory
{
	#region function

	/**
	 * ロジック生成。
	 *
	 * @template TLogicBase of LogicBase
	 * @param non-empty-string $logicClassName
	 * @phpstan-param class-string<TLogicBase> $logicClassName
	 * @param array<array-key,mixed> $arguments
	 * @return LogicBase
	 * @phpstan-return TLogicBase
	 */
	public function createLogic(string $logicClassName, array $arguments = []): LogicBase;

	#endregion
}
