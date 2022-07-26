<?php

declare(strict_types=1);

namespace PeServer\Core;

abstract class CoreUtility
{
	/**
	 * ライブラリディレクトリ パス一覧 取得。
	 *
	 * @return string[]
	 */
	public static function getLibraryDirectoryParts(): array
	{
		return ['Core', 'Libs'];
	}

	/**
	 * フォントディレクトリ パス一覧 取得。
	 *
	 * @return string[]
	 */
	public static function getFontDirectoryParts(): array
	{
		//@phpstan-ignore-next-line
		return [...self::getLibraryDirectoryParts(), 'fonts'];
	}

	/**
	 * 標準フォント パス一覧 取得。
	 *
	 * @return string[]
	 */
	public static function getDefaultFontParts(): array
	{
		//@phpstan-ignore-next-line
		return [...self::getFontDirectoryParts(), 'migmix', 'migmix-1m-regular.ttf'];
	}
}
