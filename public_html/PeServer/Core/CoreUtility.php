<?php

declare(strict_types=1);

namespace PeServer\Core;

abstract class CoreUtility
{
	public static function getLibraryDirectoryParts(): array
	{
		return ['Core', 'Libs'];
	}

	public static function getFontDirectoryParts(): array
	{
		return [...self::getLibraryDirectoryParts(), 'fonts'];
	}

	public static function getDefaultFontParts(): array
	{
		return [...self::getFontDirectoryParts(), 'migmix', 'migmix-1m-regular.ttf'];
	}
}
