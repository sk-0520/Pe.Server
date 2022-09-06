<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use PeServer\Core\Collections\Arr;
use PeServer\Core\Text;

/**
 * $_SERVER, $_COOKIE, $_SESSION 読み込みアクセス。
 *
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class SpecialStore
{
	#region function

	/**
	 * $_SERVER から値取得。
	 *
	 * @template TValue
	 * @param string $name インデックス名。
	 * @param mixed $fallbackValue 取得時失敗時の値。
	 * @phpstan-param TValue $fallbackValue
	 * @return mixed
	 * @phpstan-return TValue
	 */
	public function getServer(string $name, mixed $fallbackValue = Text::EMPTY): mixed
	{
		$result = Arr::getOr($_SERVER, $name, $fallbackValue);
		return $result;
	}

	public function tryGetServer(string $name, mixed &$result): bool
	{
		return Arr::tryGet($_SERVER, $name, $result);
	}

	/**
	 * $_SERVER に名前が存在するか。
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function containsServerName(string $name): bool
	{
		return isset($_SERVER[$name]);
	}

	/**
	 * $_SERVER の名前一覧取得。
	 *
	 * @return string[]
	 */
	public function getServerNames(): array
	{
		return Arr::getKeys($_SERVER);
	}

	/**
	 * $_COOKIE から値取得。
	 *
	 * @param string $name
	 * @param string $fallbackValue
	 * @return string
	 */
	public function getCookie(string $name, string $fallbackValue = Text::EMPTY): string
	{
		$result = Arr::getOr($_COOKIE, $name, $fallbackValue);
		return $result;
	}

	public function tryGetCookie(string $name, ?string &$result): bool
	{
		return Arr::tryGet($_COOKIE, $name, $result);
	}

	/**
	 * $_COOKIE に名前が存在するか。
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function containsCookieName(string $name): bool
	{
		return isset($_COOKIE[$name]);
	}

	/**
	 * $_COOKIE の名前一覧取得。
	 *
	 * @return string[]
	 */
	public function getCookieNames(): array
	{
		return Arr::getKeys($_COOKIE);
	}

	/**
	 * $_SESSION から値取得。
	 *
	 * @param string $name
	 * @param string $fallbackValue
	 * @return string
	 */
	public function getSession(string $name, string $fallbackValue = Text::EMPTY): string
	{
		$result = Arr::getOr($_SESSION, $name, $fallbackValue);
		return $result;
	}

	public function tryGetSession(string $name, ?string &$result): bool
	{
		return Arr::tryGet($_SESSION, $name, $result);
	}

	/**
	 * $_SESSION に名前が存在するか。
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function containsSessionName(string $name): bool
	{
		return isset($_SESSION[$name]);
	}

	/**
	 * $_SESSION の名前一覧取得。
	 *
	 * @return string[]
	 */
	public function getSessionNames(): array
	{
		return Arr::getKeys($_SESSION);
	}

	public function containsGetName(string $name): bool
	{
		return isset($_GET[$name]);
	}

	public function getGet(string $name, string $fallbackValue = Text::EMPTY): string
	{
		$result = Arr::getOr($_GET, $name, $fallbackValue);
		return $result;
	}

	public function tryGetGet(string $name, ?string &$result): bool
	{
		return Arr::tryGet($_GET, $name, $result);
	}

	/**
	 * $_GET の名前一覧取得。
	 *
	 * @return string[]
	 */
	public function getGetNames(): array
	{
		return Arr::getKeys($_GET);
	}

	public function containsPostName(string $name): bool
	{
		return isset($_POST[$name]);
	}

	public function getPost(string $name, string $fallbackValue = Text::EMPTY): string
	{
		$result = Arr::getOr($_POST, $name, $fallbackValue);
		return $result;
	}

	public function tryGetPost(string $name, ?string &$result): bool
	{
		return Arr::tryGet($_POST, $name, $result);
	}

	/**
	 * $_POST の名前一覧取得。
	 *
	 * @return string[]
	 */
	public function getPostNames(): array
	{
		return Arr::getKeys($_POST);
	}

	public function containsFileName(string $name): bool
	{
		return isset($_FILES[$name]);
	}

	// public function getFile(string $name, string $fallbackValue = Text::EMPTY): string
	// {
	// 	$result = Arr::getOr($_FILES, $name, $fallbackValue);
	// 	return $result;
	// }

	// public function tryGetFile(string $name, ?string &$result): bool
	// {
	// 	return Arr::tryGet($_FILES, $name, $result);
	// }

	/**
	 * $_FILES の名前一覧取得。
	 *
	 * @return string[]
	 */
	public function getFileNames(): array
	{
		return Arr::getKeys($_FILES);
	}

	#endregion
}
