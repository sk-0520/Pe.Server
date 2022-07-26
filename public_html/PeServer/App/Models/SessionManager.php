<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\App\Models\SessionAccount;
use PeServer\Core\InitializeChecker;
use PeServer\Core\Store\SessionStore;
use PeServer\Core\Throws\InvalidOperationException;

abstract class SessionManager
{
	public const ACCOUNT = 'account';

	/**
	 * 初期化チェック
	 */
	private static InitializeChecker $initializeChecker;

	private static SessionStore $session;

	public static function initialize(SessionStore $session): void
	{
		self::$initializeChecker ??= new InitializeChecker();
		self::$initializeChecker->initialize();

		self::$session = $session;
	}

	public static function isEnabled(): bool
	{
		self::$initializeChecker->throwIfNotInitialize();

		return self::$session->isStarted();
	}

	/**
	 * アカウントが存在するか。
	 *
	 * @return boolean
	 */
	public static function existsAccount(): bool
	{
		self::$initializeChecker->throwIfNotInitialize();

		return self::$session->tryGet(self::ACCOUNT, $unused);
	}

	/**
	 * アカウント情報取得。
	 *
	 * @return SessionAccount
	 */
	public static function getAccount(): SessionAccount
	{
		self::$initializeChecker->throwIfNotInitialize();

		if (self::$session->tryGet(self::ACCOUNT, $result)) {
			return $result;
		}

		throw new InvalidOperationException();
	}

	/**
	 * アカウント情報設定。
	 *
	 * @param SessionAccount $value
	 * @return void
	 */
	public static function setAccount(SessionAccount $value): void
	{
		self::$initializeChecker->throwIfNotInitialize();

		self::$session->set(self::ACCOUNT, $value);
	}
}
