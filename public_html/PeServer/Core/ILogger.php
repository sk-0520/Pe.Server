<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * ログ出力基盤。
 */
interface ILogger
{
	const LEVEL_TRACE = 1;
	const LEVEL_DEBUG = 2;
	const LEVEL_INFO = 3;
	const LEVEL_WARN = 4;
	const LEVEL_ERROR = 5;

	/**
	 * ログ出力。
	 *
	 * @param integer $level ログレベル。
	 * @param integer $traceIndex 現在フレーム数。
	 * @param mixed $message　メッセージ。
	 * @param mixed ...$parameters パラメータ（可変個）。
	 * @return void
	 */
	function log(int $level, int $traceIndex, $message, ...$parameters): void;
	/**
	 * トレース
	 *
	 * @param mixed $message メッセージ。
	 * @param mixed ...$parameters パラメータ（可変個）。
	 * @return void
	 */
	function trace($message, ...$parameters): void;
	/**
	 * デバッグ
	 *
	 * @param mixed $message メッセージ。
	 * @param mixed ...$parameters パラメータ（可変個）。
	 * @return void
	 */
	function debug($message, ...$parameters): void;
	/**
	 * 情報
	 *
	 * @param mixed $message メッセージ。
	 * @param mixed ...$parameters パラメータ（可変個）。
	 * @return void
	 */
	function info($message, ...$parameters): void;
	/**
	 * 警告
	 *
	 * @param mixed $message メッセージ。
	 * @param mixed ...$parameters パラメータ（可変個）。
	 * @return void
	 */
	function warn($message, ...$parameters): void;
	/**
	 * エラー
	 *
	 * @param mixed $message メッセージ。
	 * @param mixed ...$parameters パラメータ（可変個）。
	 * @return void
	 */
	function error($message, ...$parameters): void;
}
