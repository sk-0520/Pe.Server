<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplatePlugin;

use \Smarty;
use PeServer\Core\Store\CookieStore;
use PeServer\Core\Store\SessionStore;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Store\Stores;
use PeServer\Core\Store\TemporaryStore;

class TemplatePluginArgument
{
	/**
	 * 生成。
	 *
	 * @param Smarty $engine テンプレートエンジン。
	 * @param string $rootDirectoryPath ルートディレクトリ。
	 * @param string $baseDirectoryPath ベースディレクトリ。
	 * @param string $urlBasePath
	 */
	public function __construct(
		public Smarty $engine,
		public string $rootDirectoryPath,
		public string $baseDirectoryPath,
		public string $urlBasePath,
		public Stores $stores
	) {
	}
}
