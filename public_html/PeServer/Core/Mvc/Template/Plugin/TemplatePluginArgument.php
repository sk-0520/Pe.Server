<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

use Smarty;
use PeServer\Core\Environment;
use PeServer\Core\Store\CookieStore;
use PeServer\Core\Store\SessionStore;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Store\Stores;
use PeServer\Core\Store\TemporaryStore;
use PeServer\Core\Web\IUrlHelper;

class TemplatePluginArgument
{
	/**
	 * 生成。
	 *
	 * @param Smarty $engine テンプレートエンジン。
	 * @param string $rootDirectoryPath ルートディレクトリ。
	 * @param string $baseDirectoryPath ベースディレクトリ。
	 * @param IUrlHelper $urlHelper
	 * @param Environment $environment
	 */
	public function __construct(
		public Smarty $engine,
		public string $rootDirectoryPath,
		public string $baseDirectoryPath,
		public IUrlHelper $urlHelper,
		public Stores $stores,
		public Environment $environment
	) {
	}
}
