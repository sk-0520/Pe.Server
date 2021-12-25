<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\ArrayUtility;
use PeServer\Core\StringUtility;
use PeServer\Core\Throws\InvalidOperationException;

/**
 * セッション管理処理。
 *
 * 一時データとして扱い、最後にセッションへ反映する感じで動く。
 * アプリケーション側で明示的に使用しない想定。
 */
class SessionStore
{

	/**
	 * セッション一時データ。
	 *
	 * @var array<string,mixed>
	 */
	private array $_values = array();
	/**
	 * セッションは開始されているか。
	 *
	 * @var boolean
	 */
	private bool $_isStarted  = false;

	/**
	 * セッションの値に変更があったか。
	 *
	 * @var boolean
	 */
	private bool $_isChanged = false;

	private static string $sessionKey = 'PHPSESSID';

	public function __construct()
	{
		if (isset($_COOKIE[self::$sessionKey]) && !StringUtility::isNullOrWhiteSpace($_COOKIE[self::$sessionKey])) {
			$this->start();
			$this->_values = $_SESSION;
			$this->_isStarted = true;
		}
	}

	/**
	 * セッションは開始されているか。
	 *
	 * @return boolean
	 */
	public function isStarted(): bool
	{
		return $this->_isStarted;
	}

	/**
	 * セッション開始。
	 *
	 * @return void
	 * @throws InvalidOperationException 既にセッションが開始されている。
	 */
	public function start(): void
	{
		if ($this->_isStarted) {
			throw new InvalidOperationException();
		}

		session_start();
	}

	/**
	 * セッションIDの再採番。
	 *
	 * @return void
	 * @throws InvalidOperationException セッションが開始されていない。
	 */
	public function restart(): void
	{
		if (!$this->_isStarted) {
			throw new InvalidOperationException();
		}

		session_regenerate_id();
	}

	/**
	 * セッションの終了。
	 *
	 * @return void
	 */
	public function shutdown(): void
	{
		$_SESSION = array();

		if (!$this->_isStarted) {
			return;
		}

		//setcookie(self::$sessionKey, "", time() - 60);
		session_destroy();
	}

	/**
	 * セッションは変更されているか。
	 *
	 * @return boolean
	 */
	public function isChanged(): bool
	{
		return $this->_isChanged;
	}

	/**
	 * セッションデータ設定。
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function set(string $key, mixed $value): void
	{
		$this->_values[$key] = $value;
		$this->_isChanged = true;
	}

	/**
	 * セッションデータ破棄。
	 *
	 * @param string $key 対象セッションキー。空白指定ですべて削除。
	 * @return void
	 */
	public function remove(string $key): void
	{
		if (StringUtility::isNullOrEmpty($key)) {
			$_SESSION = array();
		} else {
			unset($this->_values[$key]);
		}
		$this->_isChanged = true;
	}

	/**
	 * セッションデータ取得。
	 *
	 * @param string $key
	 * @param mixed $defaultValue
	 * @return mixed 取得データ。
	 */
	public function getOr(string $key, mixed $defaultValue): mixed
	{
		return ArrayUtility::getOr($this->_values, $key, $defaultValue);
	}

	/**
	 * セッションデータ取得。
	 *
	 * @param string $key
	 * @param mixed $result
	 * @return boolean 取得できたか。
	 */
	public function tryGet(string $key, mixed &$result): bool
	{
		return ArrayUtility::tryGet($this->_values, $key, $result);
	}

	/**
	 * 一時セッションデータをセッションに反映。
	 *
	 * @return void
	 */
	public function apply(): void
	{
		$_SESSION = $this->_values;
	}
}