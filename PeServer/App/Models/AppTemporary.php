<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use DateTimeInterface;
use PeServer\Core\Code;
use PeServer\Core\Cryptography;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\Path;
use PeServer\Core\Mvc\LogicBase;
use PeServer\Core\Text;
use PeServer\Core\Web\Url;
use PeServer\Core\Web\UrlPath;
use PeServer\Core\Web\UrlQuery;

/**
 * 一時ファイルの統括処理。
 *
 * ここで指すディレクトリなどは実際の保存パスではなく、あくまで作業用のディレクトリとなる。
 */
class AppTemporary
{
	public function __construct(
		private AppConfiguration $appConfig
	) {
		//NOP
	}

	#region function

	/**
	 * 作業ディレクトリのパスを取得。
	 *
	 * @return string
	 */
	public function getBaseDirectory(): string
	{
		$path = Path::combine($this->appConfig->setting->cache->temporary, "works");

		Directory::createDirectoryIfNotExists($path);

		return $path;
	}

	/**
	 * 対象の名前のディレクトリパスを取得。
	 *
	 * @param string $name
	 * @param string|null $userId ユーザーIDが付与された場合はそのユーザーのディレクトリとなる。
	 * @return string
	 */
	private function getDirectory(string $name, ?string $userId): string
	{
		$path = Path::combine($this->getBaseDirectory(), $name);
		if (!Text::isNullOrWhiteSpace($userId)) {
			$path = Path::combine($path, $userId);
		}

		Directory::createDirectoryIfNotExists($path);
		return $path;
	}

	/**
	 * 監査ログDLディレクトリのパスを取得。
	 *
	 * 存在しなければ作成する。
	 *
	 * @param string|null $userId ユーザーIDが付与された場合はそのユーザーのディレクトリとなる。
	 * @return string
	 */
	public function getAuditLogDownloadDirectory(?string $userId): string
	{
		return $this->getDirectory("audit", $userId);
	}

	/**
	 * データベースDLディレクトリのパスを取得。
	 *
	 * 存在しなければ作成する。
	 *
	 * @param string|null $userId ユーザーIDが付与された場合はそのユーザーのディレクトリとなる。
	 * @return string
	 */
	public function getDatabaseDownloadDirectory(?string $userId): string
	{
		return $this->getDirectory("database", $userId);
	}

	/**
	 * ログDLディレクトリのパスを取得。
	 *
	 * 存在しなければ作成する。
	 *
	 * @param string|null $userId ユーザーIDが付与された場合はそのユーザーのディレクトリとなる。
	 * @return string
	 */
	public function getLogDownloadDirectory(?string $userId): string
	{
		return $this->getDirectory("logs", $userId);
	}

	/**
	 * 一時的ファイル名を適当に作成。
	 *
	 * @param DateTimeInterface $timestamp
	 * @param string $extension
	 * @return string
	 */
	public function createFileName(DateTimeInterface $timestamp, string $extension): string
	{
		$random = Cryptography::generateRandomString(16);
		return $timestamp->format('Y-m-d\_His') . "_" . $random . "." . $extension;
	}

	#endregion
}
