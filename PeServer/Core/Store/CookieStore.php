<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use PeServer\Core\Collection\Arr;
use PeServer\Core\Text;
use PeServer\Core\Store\CookieOptions;
use PeServer\Core\Store\SpecialStore;

/**
 * Cookie 管理処理。
 *
 * セッション側と違い、都度取得する感じ。
 *
 * アプリケーション側で明示的に使用しない想定。
 */
class CookieStore
{
	#region variable

	/**
	 * cookie 一時設定データ。
	 *
	 * @var array<string,LocalCookieData>
	 */
	private array $values = [];
	/**
	 * 削除データ(キー項目)。
	 *
	 * @var string[]
	 * @phpstan-var array-key[]
	 */
	private array $removes = [];

	/**
	 * クッキーの値に変更があったか。
	 */
	private bool $isChanged = false;

	#endregion

	/**
	 * 生成
	 *
	 * @param SpecialStore $special
	 * @param CookieOptions $options
	 */
	public function __construct(
		protected readonly SpecialStore $special,
		public readonly CookieOptions $options
	) {
	}

	#region function

	/**
	 * クッキーは変更されているか。
	 *
	 * @return boolean
	 */
	public function isChanged(): bool
	{
		return $this->isChanged;
	}

	/**
	 * 一時クッキーデータをセッションに反映。
	 *
	 * @return void
	 */
	public function apply(): void
	{
		foreach ($this->removes as $key) {
			if ($this->special->containsCookieName($key)) {
				setcookie($key, Text::EMPTY, time() - 60, '/');
			}
		}

		foreach ($this->values as $key => $cookie) {
			setcookie(
				$key,
				$cookie->value,
				[
					'expires' => $cookie->options->getExpires(),
					'path' => $cookie->options->path,
					'domain' => Text::EMPTY,
					'secure' => $cookie->options->secure,
					'httponly' => $cookie->options->httpOnly,
					'samesite' => $cookie->options->sameSite
				]
			);
		}
	}

	/**
	 * クッキーデータ設定。
	 *
	 * @param string $key
	 * @param string $value
	 * @param CookieOptions|null $options nullの場合コンストラクタで渡された設定値が使用される
	 * @return void
	 */
	public function set(string $key, string $value, CookieOptions $options = null): void
	{
		$this->values[$key] = new LocalCookieData($value, $options ?? $this->options);

		unset($this->removes[$key]);

		$this->isChanged = true;
	}

	/**
	 * クッキーデータ破棄。
	 *
	 * @param string $key キー。空白指定ですべて削除。
	 * @return void
	 */
	public function remove(string $key): void
	{
		if (Text::isNullOrEmpty($key)) {
			$this->values = [];
			$this->removes = $this->special->getCookieNames();
		} else {
			unset($this->values[$key]);
			$this->removes[] = $key;
		}

		$this->isChanged = true;
	}

	/**
	 * クッキーデータ取得。
	 *
	 * @param string $key
	 * @param string $fallbackValue
	 * @return string 取得データ。
	 */
	public function getOr(string $key, string $fallbackValue): string
	{
		if (Arr::tryGet($this->values, $key, $data)) {
			/** @var LocalCookieData $data */
			return $data->value;
		}

		/** @var string */
		return $this->special->getCookie($key, $fallbackValue);
	}

	/**
	 * クッキーデータ取得。
	 *
	 * @param string $key
	 * @param string $result
	 * @return boolean 取得できたか。
	 */
	public function tryGet(string $key, ?string &$result): bool
	{
		if (Arr::tryGet($this->values, $key, $data)) {
			/** @var LocalCookieData $data */
			$result = $data->value;
			return true;
		}

		return $this->special->tryGetCookie($key, $result);
	}

	#endregion
}

//phpcs:ignore PSR1.Classes.ClassDeclaration.MultipleClasses
final class LocalCookieData
{
	public function __construct(
		public string $value,
		public CookieOptions $options
	) {
	}
}
