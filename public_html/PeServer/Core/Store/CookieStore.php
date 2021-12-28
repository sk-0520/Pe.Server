<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use \DateInterval;
use PeServer\Core\ArrayUtility;
use PeServer\Core\StringUtility;
use PeServer\Core\Store\CookieOption;
use PeServer\Core\Throws\InvalidOperationException;

/**
 * cookie 管理処理。
 *
 * 一時データとして扱い、最後にセッションへ反映する感じで動く。
 * アプリケーション側で明示的に使用しない想定。
 */
class CookieStore
{
	/**
	 * 設定。
	 */
	public CookieOption $option;

	/**
	 * cookie 一時データ。
	 *
	 * @var array<string,array{data:string,option:CookieOption}>
	 */
	private array $values = array();
	/**
	 * 削除データ。
	 *
	 * @var string[]
	 */
	private array $removes = array();

	/**
	 * クッキーの値に変更があったか。
	 *
	 * @var boolean
	 */
	private bool $isChanged = false;

	/**
	 * 生成
	 *
	 * @param CookieOption $option
	 */
	public function __construct(CookieOption $option)
	{
		$this->option = $option;

		foreach ($_COOKIE as $k => $v) {
			$this->set($k, $v);
		}
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
	 * クッキーデータ設定。
	 *
	 * @param string $key
	 * @param string $value
	 * @param CookieOption|null $option nullの場合コンストラクタで渡された設定値が使用される
	 * @return void
	 */
	public function set(string $key, string $value, CookieOption $option = null): void
	{
		$this->values[$key] = [
			'data' => $value,
			'option' => $option ?? $this->$option,
		];
		unset($this->removes[$key]);
		$this->isChanged = true;
	}

	/**
	 * クッキーデータ破棄。
	 *
	 * @param string $key 対象クッキーキー。空白指定ですべて削除。
	 * @return void
	 */
	public function remove(string $key): void
	{
		if (StringUtility::isNullOrEmpty($key)) {
			$this->values = array();
			$this->removes = array_keys($_COOKIE);
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
	 * @param string $defaultValue
	 * @return string 取得データ。
	 */
	public function getOr(string $key, string $defaultValue): string
	{
		return ArrayUtility::getOr($this->values, $key, $defaultValue);
	}

	/**
	 * クッキーデータ取得。
	 *
	 * @param string $key
	 * @param string $result
	 * @return boolean 取得できたか。
	 */
	public function tryGet(string $key, string &$result): bool
	{
		return ArrayUtility::tryGet($this->values, $key, $result);
	}

	/**
	 * 一時クッキーデータをセッションに反映。
	 *
	 * @return void
	 */
	public function apply(): void
	{
		foreach ($this->removes as $key) {
			setcookie($key, '', time() - 60, '/');
		}

		foreach ($this->values as $key => $cookie) {
			$option = $cookie['option'];
			setcookie($key, $cookie['data'], $option->getTotalMinutes(), $option->path, '', $option->secure, $option->httpOnly);
		}
	}
}
