<?php

declare(strict_types=1);

namespace PeServer\Core\TemplateEngine;

use PeServer\Core\Collection\Vector;

readonly class TemplateOptions
{
	/**
	 *
	 * @param string[] $templateDirectories 読み込み対象ディレクトリパス一覧(先頭が優先される)。
	 * @param string $compileDirectory
	 * @param string $cacheDirectory
	 */
	public function __construct(
		public array $templateDirectories = [],
		public string $compileDirectory,
		public string $cacheDirectory,
	) {
		//NOP
	}
}
