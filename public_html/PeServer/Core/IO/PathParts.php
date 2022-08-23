<?php

declare(strict_types=1);

namespace PeServer\Core\IO;

use Stringable;

/**
 * パスの各要素。
 *
 * @immutable
 */
class PathParts implements Stringable
{
	/**
	 * 生成。
	 *
	 * @param string $directory ディレクトリパス(終端 ディレクトリセパレータなし)。
	 * @param string $fileName ファイル名。
	 * @param string $fileNameWithoutExtension 拡張子なしファイル名。
	 * @param string $extension 拡張子(.なし)。
	 */
	public function __construct(
		public string $directory,
		public string $fileName,
		public string $fileNameWithoutExtension,
		public string $extension,
	) {
	}

	public function toString(): string
	{
		return $this->directory . DIRECTORY_SEPARATOR . $this->fileName;
	}

	//Stringable

	public function __toString(): string
	{
		return $this->toString();
	}
}