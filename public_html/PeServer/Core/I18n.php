<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Collections\Arr;

abstract class I18n
{
	#region define

	// これはライブラリ側で持つ文字列リソース
	public const COMMON_ERROR_TITLE = '@core/common/error_title';
	public const ERROR_EMPTY = '@core/error/empty';
	public const ERROR_WHITE_SPACE = '@core/error/white_space';
	public const ERROR_LENGTH = '@core/error/length';
	public const ERROR_RANGE = '@core/error/range';
	public const ERROR_MATCH = '@core/error/match';
	public const ERROR_EMAIL = '@core/error/email';
	public const ERROR_WEBSITE = '@core/error/website';
	// ここまでライブラリ側で持つ文字列リソース

	private const INVARIANT_LOCALE = '*';

	#endregion

	#region variable

	/**
	 * 初期化チェック
	 */
	private static InitializeChecker $initializeChecker;

	/**
	 * 設定。
	 *
	 * @var array<string,string|array<string,mixed>>
	 */
	private static array $i18nConfiguration;

	#endregion

	#region function

	/**
	 * 初期化。
	 *
	 * @param array<string,string|array<string,mixed>> $i18nConfiguration
	 * @return void
	 */
	public static function initialize(array $i18nConfiguration): void
	{
		self::$initializeChecker ??= new InitializeChecker();
		self::$initializeChecker->initialize();

		self::$i18nConfiguration = $i18nConfiguration;
	}

	/**
	 * 平坦化。
	 *
	 * @param array<string,string|mixed> $array
	 * @param string $locale
	 * @return string|null
	 */
	private static function getFlatMessage(array $array, string $locale): string|null
	{
		if (isset($array[$locale])) {
			/** @var string */
			return $array[$locale];
		}

		if ($locale === self::INVARIANT_LOCALE) {
			return null;
		}

		return self::getFlatMessage($array, self::INVARIANT_LOCALE);
	}

	private static function getMessage(string $key, string $locale): string|null
	{
		if (isset(self::$i18nConfiguration[$key])) {
			// @phpstan-ignore-next-line array<string,string|array<string,mixed>>
			return self::getFlatMessage(self::$i18nConfiguration[$key], $locale);
		}

		/** @var array<string,string|array<string,mixed>> */
		$leaf = self::$i18nConfiguration;
		$tree = Text::split($key, '/');
		foreach ($tree as $node) {
			//@-phpstan-ignore-next-line
			if (Arr::tryGet($leaf, $node, $result)) {
				$leaf = $result;
			} else {
				$leaf = null;
				break;
			}
		}

		if ($leaf !== null) {
			return self::getFlatMessage($leaf, $locale) ?? Text::EMPTY;
		}


		return null;
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
	public static function message(string $key, array $parameters = []): string
	{
		self::$initializeChecker->throwIfNotInitialize();

		/** @var array<string,string> */
		$params = [];
		foreach ($parameters as $k => $v) {
			$params[strval($k)] = strval($v);
		}

		$message = self::getMessage($key, 'ja');
		if ($message === null) {
			$message =  $key;
		}

		return Text::replaceMap(Code::toLiteralString($message), $params);
	}

	#endregion
}
