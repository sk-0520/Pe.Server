<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\Log\ILogger;
use PeServer\Core\Store\Stores;

/**
 * コントローラ生成時に使用される入力値。
 * @immutable
 */
class ControllerArgument
{
	/**
	 * 生成。
	 *
	 * @param Stores $stores
	 * @param ILogger $logger ロガー。
	 */
	public function __construct(
		public Stores $stores,
		public ILogger $logger
	) {
	}
}
