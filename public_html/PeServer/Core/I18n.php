<?php

declare(strict_types=1);

namespace PeServer\Core;

abstract class I18n
{
	// これはライブラリ側で持つ文字列リソース
	public const COMMON_ERROR_TITLE = '@common-error-title';
	public const ERROR_EMPTY = '@error-empty';
	public const ERROR_WHITE_SPACE = '@error-white-space';
	public const ERROR_LENGTH = '@error-length';
	public const ERROR_RANGE = '@error-range';
	public const ERROR_MATCH = '@error-match';
	public const ERROR_EMAIL = '@error-email';
	public const ERROR_WEBSITE = '@error-website';
	// ここまでライブラリ側で持つ文字列リソース

	/**
	 * 初期化チェック
	 *
	 * @var InitializeChecker|null
	 */
	private static $_initializeChecker;

	/**
	 * Undocumented variable
	 *
	 * @var array<string,array<string,string>>
	 */
	private static array $_i18nConfiguration;

	/**
	 * Undocumented function
	 *
	 * @param array<string,mixed> $i18nConfiguration
	 * @return void
	 */
	public static function initialize(array $i18nConfiguration): void
	{
		if (is_null(self::$_initializeChecker)) {
			self::$_initializeChecker = new InitializeChecker();
		}
		self::$_initializeChecker->initialize();

		self::$_i18nConfiguration = $i18nConfiguration;
	}

	/**
	 * ローカライズ文字列を取得。
	 *
	 * 初期化時に渡された文字列リソースから対象文言を取得する。
	 *
	 * @param string $key 文字列リソースのキー。/が存在する場合にキーから見つからない場合は階層構造として扱う。
	 * @param array<int|string,int|string> $parameters
	 * @return string
	 */
	public static function message(string $key, array $parameters = array()): string
	{
		self::$_initializeChecker->throwIfNotInitialize(); // @phpstan-ignore-line null access

		/** @var array<string,string> */
		$params = [];
		foreach ($parameters as $k => $v) {
			$params[strval($k)] = strval($v);
		}

		$message = $key;

		if (isset(self::$_i18nConfiguration[$key]['*'])) {
			$message = self::$_i18nConfiguration[$key]['*'];
		}

		return StringUtility::replaceMap($message, $params);
	}
}
