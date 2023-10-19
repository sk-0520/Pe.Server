<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\Enforce;

/**
 * 環境情報。
 */
class Environment
{
	#region function

	public function __construct(
		string $locale,
		string $language,
		string $timezone,
		private string $environment,
		private string $revision
	) {
		setlocale(LC_ALL, $locale);
		mb_language($language);
		date_default_timezone_set($timezone);
	}

	public function get(): string
	{
		return $this->environment;
	}

	public function is(string $environment): bool
	{
		return $this->environment === $environment;
	}

	public function isProduction(): bool
	{
		return $this->is('production');
	}
	public function isDevelopment(): bool
	{
		return $this->is('development');
	}
	public function isTest(): bool
	{
		return $this->is('test');
	}

	public function getRevision(): string
	{
		if ($this->isProduction()) {
			return $this->revision;
		}

		return (string)time();
	}

	/**
	 * 環境変数設定。
	 *
	 * `putenv` ラッパー。
	 *
	 * @param string $name
	 * @phpstan-param non-empty-string $name
	 * @param string $value
	 * @return bool
	 * @see https://www.php.net/manual/function.putenv.php
	 */
	public static function setVariable(string $name, string $value): bool
	{
		if (Text::isNullOrWhiteSpace($name)) { //@phpstan-ignore-line [DOCTYPE]
			throw new ArgumentException($name);
		}

		return putenv($name . '=' . $value);
	}

	/**
	 * 環境変数取得。
	 *
	 * `getenv` ラッパー。
	 *
	 * @param string $name
	 * @phpstan-param non-empty-string $name
	 * @return string|null 環境変数の値。取得できなかった場合に null。
	 * @see https://www.php.net/manual/function.getenv.php
	 */
	public static function getVariable(string $name): ?string
	{
		if (Text::isNullOrWhiteSpace($name)) { //@phpstan-ignore-line [DOCTYPE]
			throw new ArgumentException($name);
		}

		$result = getenv($name);
		if ($result === false) {
			return null;
		}

		return $result;
	}

	#endregion
}
