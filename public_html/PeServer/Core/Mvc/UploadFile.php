<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\IO\Directory;
use PeServer\Core\Throws\InvalidOperationException;

/**
 * アップロードファイル。
 *
 * @see https://www.php.net/manual/features.file-upload.post-method.php
 */
class UploadFile
{
	/**
	 * 生成。
	 *
	 * @param string $originalFileName [信頼できない情報] アップロード要求ファイル名。
	 * @param string $uploadedFilePath アップロードされたファイルパス。パス自体はPHP管理下の一時ファイルパスとなる。
	 * @param string $mime [信頼できない情報] アップロードされたファイルのMIME。
	 * @param int $fileSize アップロードファイルサイズ。
	 * @phpstan-param UnsignedIntegerAlias $fileSize
	 * @param int $errorCode エラーコード。
	 */
	public function __construct(
		public string $originalFileName,
		public string $uploadedFilePath,
		public string $mime,
		public int $fileSize,
		public int $errorCode,
	) {
	}

	#region function

	/**
	 * `$_FILE[name]` から `UploadFile` を生成。
	 *
	 * @param array<string,string|int> $file
	 * @return self
	 */
	public static function create(array $file): self
	{
		/** @phpstan-var UnsignedIntegerAlias */
		$size = (int)$file['size'];

		return new self(
			(string)$file['name'],
			(string)$file['tmp_name'],
			(string)$file['type'],
			$size,
			(int)$file['error']
		);
	}

	/**
	 * 原則使用する必要なし。
	 *
	 * だってもうアップロードファイルからしか生成されないし。。。 ラッパー側で使う系かなぁ。。。わからん。
	 *
	 * @return bool
	 * @see https://www.php.net/manual/function.is-uploaded-file.php
	 */
	public function isEnabled(): bool
	{
		return is_uploaded_file($this->uploadedFilePath);
	}

	/**
	 * アップロードされたファイルを移動。
	 *
	 * @param string $path
	 * @see https://www.php.net/manual/function.move-uploaded-file.php
	 */
	public function move(string $path): void
	{
		Directory::createParentDirectoryIfNotExists($path);

		$result = move_uploaded_file($this->uploadedFilePath, $path);
		if (!$result) {
			throw new InvalidOperationException($path);
		}
	}

	#endregion
}
