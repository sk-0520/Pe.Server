<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use PeServer\Core\Security;
use PeServer\Core\FileUtility;
use PeServer\Core\ArrayUtility;
use PeServer\Core\InitialValue;
use PeServer\Core\StringUtility;
use PeServer\Core\Store\CookieStore;
use PeServer\Core\Store\SessionOption;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Throws\InvalidOperationException;

/**
 * セッション管理処理。
 *
 * 一時データとして扱い、最後にセッションへ反映する感じで動く。
 * アプリケーション側で明示的に使用しない想定。
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class SessionStore
{
	public const APPLY_NORMAL = 0;
	public const APPLY_CANCEL = 1;
	public const APPLY_RESTART = 2;
	public const APPLY_SHUTDOWN = 3;

	/** @readonly */
	private SessionOption $option;
	/** @readonly */
	private CookieStore $cookie;

	/**
	 * セッション一時データ。
	 *
	 * @var array<string,mixed>
	 */
	private array $values = array();
	/**
	 * セッションは開始されているか。
	 */
	private bool $isStarted  = false;

	/**
	 * セッション適用状態。
	 *
	 * @var integer
	 * @phpstan-var self::APPLY_*
	 */
	private int $applyState = self::APPLY_NORMAL;

	/**
	 * セッションの値に変更があったか。
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
	 * セッション適用状態を設定。
	 *
	 * @param integer $state
	 * @return integer
	 * @phpstan-param self::APPLY_* $state
	 * @phpstan-return self::APPLY_*
	 */
	public function setApplyState(int $state): int
	{
		$oldValue = $this->applyState;

		$this->applyState = $state;

		return $oldValue;
	}

	private function applyCore(): void
	{
		$_SESSION = $this->values;
		session_write_close();
	}

	/**
	 * 一時セッションデータを事前指定された適用種別に応じて反映。
	 *
	 * @return void
	 */
	public function apply(): void
	{
		switch ($this->applyState) {
			case self::APPLY_NORMAL:
				if ($this->isChanged()) {
					if (!$this->isStarted()) {
						$this->start();
					}
					$this->applyCore();
				}
				break;

			case self::APPLY_CANCEL:
				// なんもしない
				break;

			case self::APPLY_RESTART:
				if ($this->isStarted()) {
					$this->restart();
				} else {
					$this->start();
				}
				$this->applyCore();
				break;

			case self::APPLY_SHUTDOWN:
				if ($this->isStarted()) {
					$this->shutdown();
				}
				break;

			default:
				throw new NotImplementedException();
		}
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

		$sessionOption = [
			'lifetime' => $this->option->cookie->getExpires(),
			'path' => $this->option->cookie->path,
			'domain' => InitialValue::EMPTY_STRING,
			'secure' => $this->option->cookie->secure,
			'httponly' => $this->option->cookie->httpOnly,
			'samesite' => $this->option->cookie->sameSite,
		];
		session_set_cookie_params($sessionOption);

		session_start();

		// セッションにCSRFトークンが存在しない場合は生成
		/**  */
		if (!ArrayUtility::tryGet($_SESSION, Security::CSRF_SESSION_KEY, $unused)) {
			$csrfToken = Security::generateCsrfToken();
			$this->set(Security::CSRF_SESSION_KEY, $csrfToken);
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
	 * @phpstan-param ServerStoreValueAlias $value
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
	 * @param mixed $fallbackValue
	 * @phpstan-param ServerStoreValueAlias $fallbackValue
	 * @return mixed 取得データ。
	 * @phpstan-return ServerStoreValueAlias
	 */
	public function getOr(string $key, mixed $fallbackValue): mixed
	{
		return ArrayUtility::getOr($this->values, $key, $fallbackValue);
	}

	/**
	 * セッションデータ取得。
	 *
	 * @param string $key
	 * @param mixed $result
	 * @phpstan-param ServerStoreValueAlias $result
	 * @return boolean 取得できたか。
	 */
	public function tryGet(string $key, mixed &$result): bool
	{
		return ArrayUtility::tryGet($this->values, $key, $result);
	}
}
