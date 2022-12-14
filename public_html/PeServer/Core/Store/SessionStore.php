<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use PeServer\Core\Collections\Arr;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\IOUtility;
use PeServer\Core\Security;
use PeServer\Core\Store\CookieStore;
use PeServer\Core\Store\SessionOption;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Throws\NotImplementedException;

/**
 * セッション管理処理。
 *
 * 一時データとして扱い、最後にセッションへ反映する感じで動く。
 * アプリケーション側で明示的に使用しない想定。
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class SessionStore
{
	#region define

	public const APPLY_NORMAL = 0;
	public const APPLY_CANCEL = 1;
	public const APPLY_RESTART = 2;
	public const APPLY_SHUTDOWN = 3;

	#endregion

	#region variable

	/** @readonly */
	private SessionOption $option;
	/** @readonly */
	private CookieStore $cookie;

	/**
	 * セッション一時データ。
	 *
	 * @var array<string,mixed>
	 */
	private array $values = [];
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

	#endregion

	/**
	 * 生成
	 *
	 * @param SessionOption $option セッション設定。
	 * @param CookieStore $cookie Cookie 設定。
	 */
	public function __construct(SessionOption $option, CookieStore $cookie)
	{
		if (Text::isNullOrWhiteSpace($option->name)) { //@phpstan-ignore-line
			throw new ArgumentException('$option->name');
		}

		$this->option = $option;
		$this->cookie = $cookie;

		if ($this->cookie->tryGet($this->option->name, $nameValue)) {
			if (!Text::isNullOrWhiteSpace($nameValue)) {
				$this->start();
				$this->values = $_SESSION;
				$this->isStarted = true;
			}
		}
	}

	#region function

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

		// セッション名はコンストラクタ時点で設定済みのためチェックしない
		session_name($this->option->name);

		if (!Text::isNullOrWhiteSpace($this->option->savePath)) {
			Directory::createDirectoryIfNotExists($this->option->savePath);
			session_save_path($this->option->savePath);
		}

		$sessionOption = [
			'lifetime' => $this->option->cookie->getExpires(),
			'path' => $this->option->cookie->path,
			'domain' => Text::EMPTY,
			'secure' => $this->option->cookie->secure,
			'httponly' => $this->option->cookie->httpOnly,
			'samesite' => $this->option->cookie->sameSite,
		];
		session_set_cookie_params($sessionOption);

		session_start();

		// セッションにCSRFトークンが存在しない場合は生成
		/**  */
		if (!Arr::tryGet($_SESSION, Security::CSRF_SESSION_KEY, $unused)) {
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
		$_SESSION = [];

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
		if (Text::isNullOrEmpty($key)) {
			$this->values = [];
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
		return Arr::getOr($this->values, $key, $fallbackValue);
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
		return Arr::tryGet($this->values, $key, $result);
	}

	#endregion
}
