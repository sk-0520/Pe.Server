<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use PeServer\Core\Collections\Arr;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Mvc\UploadFile;
use PeServer\Core\Text;
use PeServer\Core\Web\Url;
use PeServer\Core\Web\UrlPath;
use PeServer\Core\Web\UrlQuery;

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

	public function getFile(string $name): UploadFile
	{
		if (!isset($_FILES[$name])) {
			return UploadFile::invalid($name);
		}

		$file = $_FILES[$name];
		return UploadFile::create($file);
	}

	public function tryGetFile(string $name, ?UploadFile &$result): bool
	{
		if (Arr::tryGet($_FILES, $name, $file)) {
			$result = UploadFile::create($file);
			return true;
		}

		return false;
	}

	/**
	 * $_FILES の名前一覧取得。
	 *
	 * @return string[]
	 */
	public function getFileNames(): array
	{
		return Arr::getKeys($_FILES);
	}

	public function getServerName(): string
	{
		return $this->getServer('SERVER_NAME');
	}

	public function isHttps(): bool
	{
		return $this->getServer('HTTPS') === 'on';
	}

	public function isLocalhost(): bool
	{
		return Arr::in(
			[
				'loc!alhost',
				'127.0.0.1',
			],
			$this->getServerName()
		);
	}

	public function getPort(): int
	{
		$raw = $this->getServer('SERVER_PORT', null);
		// if (Text::isNullOrEmpty($raw)) {
		// 	return null;
		// }

		return (int)$raw;
	}

	public function getHost(): string
	{
		return $this->getServer('HTTP_HOST', TEXT::EMPTY);
	}

	private function getServerUrlCore(bool $withPathInfo): Url
	{
		$isHttps = $this->isHttps();

		$port = $this->getPort();
		if ($isHttps) {
			if ($port === 443) {
				$port = '';
			}
		} else {
			if ($port === 80) {
				$port = '';
			}
		}
		if ($port !== '') {
			$port = ":$port";
		}

		$query = $this->getServer('QUERY_STRING', Text::EMPTY);
		if (Text::isNullOrEmpty($query)) {
			$query = null;
		}

		$url = $isHttps ? 'https://' : 'http://';
		$url .= $this->getHost();
		$url .= $port;
		if ($withPathInfo) {
			$url .= $this->getServer('REQUEST_URI', Text::EMPTY);
		}

		return Url::parse($url);
	}

	public function getServerUrl(): Url
	{
		return $this->getServerUrlCore(false);
	}

	/**
	 * URLを取得。
	 *
	 * リバースプロキシだったり認証だったりの細かい制御は行っていない。
	 *
	 * @return Url
	 */
	public function getRequestUrl(): Url
	{
		return $this->getServerUrlCore(true);
	}

	public function getRequestMethod(): HttpMethod
	{
		$raw = $this->getServer('REQUEST_METHOD', Text::EMPTY);

		return HttpMethod::from(Text::toUpper(Text::trim($raw)));
	}




	#endregion
}
