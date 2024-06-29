<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\IO\Directory;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Throws\NotSupportedException;

/**
 * アップロードファイル。
 *
 * @see https://www.php.net/manual/features.file-upload.post-method.php
 */
class UploadedFile
{
	/**
	 * 生成。
	 *
	 * @param string $originalFileName [信頼できない情報] アップロード要求ファイル名。
	 * @param string $uploadedFilePath アップロードされたファイルパス。パス自体はPHP管理下の一時ファイルパスとなる。
	 * @param string $mime [信頼できない情報] アップロードされたファイルのMIME。
	 * @param int $fileSize アップロードファイルサイズ。
	 * @phpstan-param non-negative-int $fileSize
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
		/** @phpstan-var non-negative-int */
		$size = (int)$file['size'];

		return new self(
			(string)$file['name'],
			(string)$file['tmp_name'],
			(string)$file['type'],
			$size,
			(int)$file['error']
		);
	}

	public static function invalid(string $key): self
	{
		return new LocalInvalidUploadedFile($key);
	}

	/**
	 * アップロードされたファイルか。
	 *
	 * 非アップロード判定にも使用可能。
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

//phpcs:ignore PSR1.Classes.ClassDeclaration.MultipleClasses
class LocalInvalidUploadedFile extends UploadedFile
{
	public function __construct(
		public readonly string $key
	) {
		parent::__construct(
			'',
			'',
			'',
			0,
			-1
		);
	}

	public function isEnabled(): bool
	{
		return false;
	}

	public function move(string $path): void
	{
		throw new NotSupportedException();
	}
}
