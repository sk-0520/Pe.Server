<?php

declare(strict_types=1);

namespace PeServer\App\Models\Configuration;

use PeServer\Core\Log\ILogger;
use PeServer\Core\Serialization\Mapping;

/**
 * ロガー設定。
 *
 * @immutable
 */
class LoggerSetting
{
	#region variable

	/**
	 * ロガークラス。
	 *
	 * @phpstan-var class-string<ILogger>
	 */
	#[Mapping(name: 'logger_class')]
	public string $loggerClass;

	/**
	 * 対象ログレベル。
	 *
	 * @phpstan-var ILogger::LOG_LEVEL_*
	 */
	public int $level;

	/**
	 * フォーマット。
	 *
	 * @phpstan-var literal-string
	 */
	public string $format;

	/**
	 * ロガー独自設定。
	 *
	 * @var array<string,mixed>
	 */
	public array $configuration = [];

	#endregion
}
