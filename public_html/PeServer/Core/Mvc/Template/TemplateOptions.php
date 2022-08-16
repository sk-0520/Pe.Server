<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template;

use PeServer\Core\Web\IUrlHelper;

/**
 * テンプレート生成設定。
 */
class TemplateOptions
{
	/**
	 * 生成。
	 *
	 * 注釈の元になるパス: `__DIR__\template\base\template.tpl`
	 *
	 * @param string $rootDirectoryPath テンプレートファイル配置ディレクトリのルートパス。(※ `__DIR__\template`)
	 * @param string $baseDirectoryName テンプレートファイルベースディレクトリの名前。(※ `name`)
	 * @param IUrlHelper $urlHelper URLベースパス
	 * @param string $temporaryDirectoryPath 一時ディレクトリパス。テンプレートエンジンによりけりだけどコンパイル結果とかキャッシュとかを配置する親元。
	 */
	public function __construct(
		public string $rootDirectoryPath,
		public string $baseDirectoryName,
		public IUrlHelper $urlHelper,
		public string $temporaryDirectoryPath
	) {
	}
}
