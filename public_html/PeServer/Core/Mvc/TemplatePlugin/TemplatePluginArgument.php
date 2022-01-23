<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplatePlugin;

use \Smarty;

class TemplatePluginArgument
{
	/**
	 * 生成。
	 *
	 * @param Smarty $engine テンプレートエンジン。
	 * @param string $rootDirectoryPath ルートディレクトリ。
	 * @param string $baseDirectoryPath ベースディレクトリ。
	 */
	public function __construct(
		public Smarty $engine,
		public string $rootDirectoryPath,
		public string $baseDirectoryPath
	) {
	}
}
