<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

/**
 * ログ出力インターフェイス。
 *
 * !!注意!! PSR-3 には準拠していない。
 */
interface ILogger
{
	/** レベル: トレース */
	public const LOG_LEVEL_TRACE = 1;
	/** レベル: デバッグ */
	public const LOG_LEVEL_DEBUG = 2;
	/** レベル: 情報 */
	public const LOG_LEVEL_INFORMATION = 3;
	/** レベル: 警告 */
	public const LOG_LEVEL_WARNING = 4;
	/** レベル: エラー */
	public const LOG_LEVEL_ERROR = 5;

	/**
	 * ログ出力。
	 *
	 * アプリケーション層で呼び出すことはない。
	 *
	 * @param integer $level ログレベル。
	 * @phpstan-param self::LOG_LEVEL_* $level ログレベル。
	 * @param integer $traceIndex 現在フレーム数。
	 * @param mixed $message メッセージかオブジェクト。
	 * @phpstan-param LogMessageAlias $message
	 * @param mixed ...$parameters パラメータ（可変個）。$messageが文字列の場合はプレースホルダー {\d} に対して置き換え処理が行われるがその場合は所謂0始まり・抜けなしの配列を想定している。
	 * @return void
	 */
	function log(int $level, int $traceIndex, $message, ...$parameters): void;
	/**
	 * トレース
	 *
	 * @param mixed $message メッセージかオブジェクト。
	 * @phpstan-param LogMessageAlias $message
	 * @param mixed ...$parameters パラメータ（可変個）。$messageが文字列の場合はプレースホルダー {\d} に対して置き換え処理が行われるがその場合は所謂0始まり・抜けなしの配列を想定している。
	 * @return void
	 */
	function trace($message, ...$parameters): void;
	/**
	 * デバッグ
	 *
	 * @param mixed $message メッセージかオブジェクト。
	 * @phpstan-param LogMessageAlias $message
	 * @param mixed ...$parameters パラメータ（可変個）。$messageが文字列の場合はプレースホルダー {\d} に対して置き換え処理が行われるがその場合は所謂0始まり・抜けなしの配列を想定している。
	 * @return void
	 */
	function debug($message, ...$parameters): void;
	/**
	 * 情報
	 *
	 * @param mixed $message メッセージかオブジェクト。
	 * @phpstan-param LogMessageAlias $message
	 * @param mixed ...$parameters パラメータ（可変個）。$messageが文字列の場合はプレースホルダー {\d} に対して置き換え処理が行われるがその場合は所謂0始まり・抜けなしの配列を想定している。
	 * @return void
	 */
	function info($message, ...$parameters): void;
	/**
	 * 警告
	 *
	 * @param mixed $message メッセージかオブジェクト。
	 * @phpstan-param LogMessageAlias $message
	 * @param mixed ...$parameters パラメータ（可変個）。$messageが文字列の場合はプレースホルダー {\d} に対して置き換え処理が行われるがその場合は所謂0始まり・抜けなしの配列を想定している。
	 * @return void
	 */
	function warn($message, ...$parameters): void;
	/**
	 * エラー
	 *
	 * @param mixed $message メッセージかオブジェクト。
	 * @phpstan-param LogMessageAlias $message
	 * @param mixed ...$parameters パラメータ（可変個）。$messageが文字列の場合はプレースホルダー {\d} に対して置き換え処理が行われるがその場合は所謂0始まり・抜けなしの配列を想定している。
	 * @return void
	 */
	function error($message, ...$parameters): void;
}
