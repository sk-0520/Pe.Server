<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use PeServer\Core\Text;

abstract class SessionHandlerFactoryUtility
{
	#region function

	/**
	 * 対象のクラス名が `ISessionHandlerFactory` を実装しているか。
	 *
	 * @param string|null $name
	 * @return bool
	 * @phpstan-assert-if-true class-string<ISessionHandlerFactory> $name
	 */
	public static function isFactory(?string $name): bool
	{
		if (Text::isNullOrWhiteSpace($name)) {
			return false;
		}

		if (!class_exists($name)) {
			return false;
		}

		if (!class_exists($name)) {
			return false;
		}

		return is_subclass_of($name, ISessionHandlerFactory::class);
	}

	#endregion
}
