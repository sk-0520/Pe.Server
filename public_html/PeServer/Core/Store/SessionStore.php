<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use \PeServer\Core\Csrf;
use \PeServer\Core\ArrayUtility;
use PeServer\Core\FileUtility;
use \PeServer\Core\StringUtility;
use \PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\CoreError;
use \PeServer\Core\Throws\InvalidOperationException;

/**
 * セッション管理処理。
 *
 * 一時データとして扱い、最後にセッションへ反映する感じで動く。
 * アプリケーション側で明示的に使用しない想定。
 */
class SessionStore
{
	private SessionOption $option;
	private CookieStore $cookie;

	/**
	 * セッション一時データ。
	 *
	 * @var array<string,mixed>
	 */
	private array $values = array();
	/**
	 * セッションは開始されているか。
	 *
	 * @var boolean
	 */
	private bool $isStarted  = false;

	/**
	 * セッションの値に変更があったか。
	 *
	 * @var boolean
	 */
	private bool $isChanged = false;

	public function __construct(SessionOption $option, CookieStore $cookie)
	{
		if (StringUtility::isNullOrWhiteSpace($option->name)) {
			throw new ArgumentException('$option->name');
		}

		$this->option = $option;
		$this->cookie = $cookie;

		if ($this->cookie->tryGet($this->option->name, $nameValue)) {
			if (!StringUtility::isNullOrWhiteSpace($nameValue)) {
				$this->start();
				$this->values = $_SESSION;
				$this->isStarted = true;
			}
		}
	}

	/**
	 * 一時セッションデータをセッションに反映。
	 *
	 * @return void
	 */
	public function apply(): void
	{
		$_SESSION = $this->values;
		session_write_close();
	}

	/**
	 * セッションは開始されているか。
	 *
	 * @return boolean
	 */
	public function isStarted(): bool
	{
		return $this->isStarted;
	}

	/**
	 * セッション開始。
	 *
	 * @return void
	 * @throws InvalidOperationException 既にセッションが開始されている。
	 */
	public function start(): void
	{
		if ($this->isStarted) {
			throw new InvalidOperationException();
		}

		if (!StringUtility::isNullOrWhiteSpace($this->option->name)) {
			session_name($this->option->name);
		}

		if (!StringUtility::isNullOrWhiteSpace($this->option->savePath)) {
			FileUtility::createDirectoryIfNotExists($this->option->savePath);
			session_save_path($this->option->savePath);
		}

		session_start();

		// セッションにCSRFトークンが存在しない場合は生成
		if (!ArrayUtility::tryGet($_SESSION, Csrf::SESSION_KEY, $_)) {
			$csrfToken = Csrf::generateToken();
			$this->set(Csrf::SESSION_KEY, $csrfToken);
		}
	}

	/**
	 * セッションIDの再採番。
	 *
	 * @return void
	 * @throws InvalidOperationException セッションが開始されていない。
	 */
	public function restart(): void
	{
		if (!$this->isStarted) {
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

		if (!$this->isStarted) {
			return;
		}

		$this->cookie->remove($this->option->name);
		session_destroy();
	}

	/**
	 * セッションは変更されているか。
	 *
	 * @return boolean
	 */
	public function isChanged(): bool
	{
		return $this->isChanged;
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
		$this->values[$key] = $value;
		$this->isChanged = true;
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
			$this->values = array();
		} else {
			unset($this->values[$key]);
		}

		$this->isChanged = true;
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
		return ArrayUtility::getOr($this->values, $key, $defaultValue);
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
		return ArrayUtility::tryGet($this->values, $key, $result);
	}
}
