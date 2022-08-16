<?php

declare(strict_types=1);

namespace PeServer\Core;

abstract class CoreUtility
{
	/** ライブラリディレクトリパス */
	public const LIBRARY_DIRECTORY_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'Libs';
	/** フォントディレクトリパス */
	public const FONT_DIRECTORY_PATH = self::LIBRARY_DIRECTORY_PATH . DIRECTORY_SEPARATOR . 'fonts';
	/** 標準フォントパス */
	public const DEFAULT_FONT_FILE_PATH = self::FONT_DIRECTORY_PATH . DIRECTORY_SEPARATOR . 'migmix' . DIRECTORY_SEPARATOR . 'migmix-1m-regular.ttf';
}
