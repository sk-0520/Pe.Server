<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Mvc\Template\ITemplateFactory;
use PeServer\Core\Store\Stores;
use PeServer\Core\Web\IUrlHelper;
use PeServer\Core\WebSecurity;

/**
 * コントローラ生成時に使用される入力値。
 */
readonly class ControllerArgument
{
	/**
	 * 生成。
	 *
	 * @param Stores $stores
	 * @param ILogger $logger ロガー。
	 */
	public function __construct(
		public Stores $stores,
		public ILogicFactory $logicFactory,
		public ITemplateFactory $templateFactory,
		public IUrlHelper $urlHelper,
		public WebSecurity $webSecurity,
		public ILoggerFactory $loggerFactory,
		public ILogger $logger,
	) {
	}
}
