<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\Mvc\LogicParameter;

interface ILogicFactory
{
	#region function

	/**
	 * ロジック生成。
	 *
	 * @template TLogicBase of LogicBase
	 * @param string $logicClassName
	 * @phpstan-param class-string<TLogicBase> $logicClassName
	 * @param array<int|string,mixed> $arguments
	 * @return LogicBase
	 * @phpstan-return TLogicBase
	 */
	public function createLogic(string $logicClassName, array $arguments = []): LogicBase;

	#endregion
}
