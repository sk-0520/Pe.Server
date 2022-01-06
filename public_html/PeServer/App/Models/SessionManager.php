<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\CoreInitializer;
use PeServer\Core\InitializeChecker;
use PeServer\Core\Store\SessionStore;
use PeServer\App\Models\AppConfiguration;
use PeServer\Core\Throws\CoreException;
use PeServer\Core\Throws\InvalidOperationException;

abstract class SessionManager
{
	public const ACCOUNT = 'account';

	/**
	 * 初期化チェック
	 *
	 * @var InitializeChecker|null
	 */
	private static $initializeChecker;

	private static SessionStore $session;

	public static function initialize(SessionStore $session): void
	{
		if (is_null(self::$initializeChecker)) {
			self::$initializeChecker = new InitializeChecker();
		}
		self::$initializeChecker->initialize();

		self::$session = $session;
	}

	public static function isEnabled(): bool
	{
		self::$initializeChecker->throwIfNotInitialize(); // @phpstan-ignore-line null access

		return self::$session->isStarted();
	}

	public static function existsAccount(): bool
	{
		self::$initializeChecker->throwIfNotInitialize(); // @phpstan-ignore-line null access

		return self::$session->tryGet(self::ACCOUNT, $_);
	}

	/**
	 * アカウント情報取得。
	 *
	 * @return array{user_id:string,login_id:string,name:string,level:string,state:string}
	 */
	public static function getAccount(): array
	{
		self::$initializeChecker->throwIfNotInitialize(); // @phpstan-ignore-line null access

		if (self::$session->tryGet(self::ACCOUNT, $result)) {
			return $result;
		}

		throw new InvalidOperationException();
	}

	/**
	 * アカウント情報設定。
	 *
	 * @param array{user_id:string,login_id:string,name:string,level:string,state:string} $value
	 * @return void
	 */
	public static function setAccount(array $value): void
	{
		self::$initializeChecker->throwIfNotInitialize(); // @phpstan-ignore-line null access

		self::$session->set(self::ACCOUNT, $value);
	}
}
