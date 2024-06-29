<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

use Smarty\Smarty;
use PeServer\Core\Environment;
use PeServer\Core\ProgramContext;
use PeServer\Core\Store\CookieStore;
use PeServer\Core\Store\SessionStore;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Store\Stores;
use PeServer\Core\Store\TemporaryStore;
use PeServer\Core\Web\IUrlHelper;
use PeServer\Core\Web\WebSecurity;

class TemplatePluginArgument
{
	/**
	 * 生成。
	 *
	 * @param Smarty $engine テンプレートエンジン。
	 * @param string $rootDirectoryPath テンプレートファイルルートディレクトリのパス。
	 * @param string $baseDirectoryPath テンプレートファイルベースディレクトリのパス。
	 * @param ProgramContext $programContext
	 * @param IUrlHelper $urlHelper
	 * @param WebSecurity $webSecurity
	 * @param Environment $environment
	 */
	public function __construct(
		public Smarty $engine,
		public string $rootDirectoryPath,
		public string $baseDirectoryPath,
		public ProgramContext $programContext,
		public IUrlHelper $urlHelper,
		public WebSecurity $webSecurity,
		public Stores $stores,
		public Environment $environment
	) {
	}
}
