<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use LogicException;
use PeServer\Core\ArrayUtility;
use PeServer\Core\StringUtility;

class SessionStore
{
	public const NEXT_STATE_NORMAL = 0; // こいつらはここに所属する必要ないけどこのためだけにファイル分割はちょっとつらいので妥協
	public const NEXT_STATE_CANCEL = 1;
	public const NEXT_STATE_RESTART = 2;
	public const NEXT_STATE_SHUTDOWN = 3;

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

	private bool $_isChanged = false;

	private static string $sessionKey = 'PHPSESSID';

	public function __construct()
	{
		if ($_COOKIE[self::$sessionKey]) {
			$this->start();
			$this->_values = $_SESSION;
			$this->_isStarted = true;
		}
	}

	public function isStarted(): bool
	{
		return $this->_isStarted;
	}

	public function start(): void
	{
		if ($this->_isStarted) {
			throw new LogicException();
		}

		session_start();
	}

	public function restart(): void
	{
		if (!$this->_isStarted) {
			throw new LogicException();
		}

		session_regenerate_id();
	}

	public function shutdown(): void
	{
		$_SESSION = array();

		if (!$this->_isStarted) {
			return;
		}

		session_destroy();
	}

	public function isChanged(): bool
	{
		return $this->_isChanged;
	}

	public function set(string $key, mixed $value): void
	{
		$this->_values[$key] = $value;
		$this->_isChanged = true;
	}

	public function remove(string $key): void
	{
		if (StringUtility::isNullOrEmpty($key)) {
			$_SESSION = array();
		} else {
			unset($this->_values[$key]);
		}
		$this->_isChanged = true;
	}

	public function getOr(string $key, mixed $defaultValue): mixed
	{
		return ArrayUtility::getOr($this->_values, $key, $defaultValue);
	}

	public function tryGet(string $key, mixed &$result): bool
	{
		return ArrayUtility::tryGet($this->_values, $key, $result);
	}

	public function apply(): void
	{
		$_SESSION = $this->_values;
	}
}
