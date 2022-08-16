<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use PeServer\Core\ArrayUtility;
use PeServer\Core\DefaultValue;
use PeServer\Core\Text;
use PeServer\Core\Store\CookieOption;
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

	/**
	 * 生成
	 *
	 * @param CookieOption $option
	 */
	public function __construct(
		/** @readonly */
		protected SpecialStore $special,
		/** @readonly */
		public CookieOption $option
	) {
	}

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
				setcookie($key, DefaultValue::EMPTY_STRING, time() - 60, '/');
			}
		}

		foreach ($this->values as $key => $cookie) {
			setcookie(
				$key,
				$cookie->value,
				[
					'expires' => $cookie->option->getExpires(),
					'path' => $cookie->option->path,
					'domain' => DefaultValue::EMPTY_STRING,
					'secure' => $cookie->option->secure,
					'httponly' => $cookie->option->httpOnly,
					'samesite' => $cookie->option->sameSite
				]
			);
		}
	}

	/**
	 * クッキーデータ設定。
	 *
	 * @param string $key
	 * @param string $value
	 * @param CookieOption|null $option nullの場合コンストラクタで渡された設定値が使用される
	 * @return void
	 */
	public function set(string $key, string $value, CookieOption $option = null): void
	{
		$this->values[$key] = new LocalCookieData($value, $option ?? $this->option);

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
		if (ArrayUtility::tryGet($this->values, $key, $data)) {
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
		if (ArrayUtility::tryGet($this->values, $key, $data)) {
			/** @var LocalCookieData $data */
			$result = $data->value;
			return true;
		}

		return $this->special->tryGetCookie($key, $result);
	}
}

final class LocalCookieData
{
	public function __construct(
		public string $value,
		public CookieOption $option
	) {
	}
}
