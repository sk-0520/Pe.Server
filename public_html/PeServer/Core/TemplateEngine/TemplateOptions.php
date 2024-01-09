<?php

declare(strict_types=1);

namespace PeServer\Core\TemplateEngine;

use PeServer\Core\Collection\Vector;
use PeServer\Core\Encoding;

readonly class TemplateOptions
{
	/**
	 * 生成。
	 * @param string[] $templateDirectories 読み込み対象ディレクトリパス一覧(先頭が優先される)。
	 * @param string $compileDirectory
	 * @param string $cacheDirectory
	 * @param ITemplateVariableFilter $variableFilter
	 */
	public function __construct(
		public array $templateDirectories,
		public string $compileDirectory,
		public string $cacheDirectory,
		public ITemplateVariableFilter $variableFilter,
	) {
		//NOP
	}
}
