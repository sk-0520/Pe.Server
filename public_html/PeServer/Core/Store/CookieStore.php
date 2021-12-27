<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use \DateInterval;
use PeServer\Core\ArrayUtility;
use PeServer\Core\StringUtility;
use PeServer\Core\Throws\InvalidOperationException;

/**
 * cookie 管理処理。
 *
 * 一時データとして扱い、最後にセッションへ反映する感じで動く。
 * アプリケーション側で明示的に使用しない想定。
 */
class CookieStore
{
	private bool $secure = false;
	private bool $httpOnly = true;

	/**
	 * cookie 一時データ。
	 *
	 * @var array<string,array{data:string,path:string,span:DateInterval|null,secure:bool,httpOnly:bool}>
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

	public function __construct()
	{
		//TODO: session_get_cookie_params
		foreach ($_COOKIE as $k => $v) {
			$options = [
				'path' => '/',
				'span' => null,
				'secure' => $this->secure,
				'httpOnly' => $this->httpOnly,
			];
			$this->set($k, $v, $options);
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
	 * @param array{path:?string,span:?DateInterval,secure:?bool,httpOnly:?bool} $options
	 * @return void
	 */
	public function set(string $key, string $value, array $options): void
	{
		$this->values[$key] = [
			'data' => $value,
			'path' => ArrayUtility::getOr($options, 'path', ''),
			'span' => ArrayUtility::getOr($options, 'span', null),
			'secure' => ArrayUtility::getOr($options, 'secure', $this->secure),
			'httpOnly' => ArrayUtility::getOr($options, 'httpOnly', $this->httpOnly),
		];
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
			$span = 0;
			if (!is_null($cookie['span'])) {
				/** @var DateInterval */
				$time = $span;
				$span = ($time->d * 24 * 60) + ($time->h * 60) + $time->i;
			}
			setcookie($key, $cookie['data'], $span, $cookie['path'], '', $cookie['secure'], $cookie['httpOnly']);
		}
	}
}
