<?php

declare(strict_types=1);

namespace PeServer\Core;

use \PeServer\Core\ILogger;

/**
 * コントローラ生成時に使用される入力値。
 */
class ControllerArguments
{
	/**
	 * ロガー
	 *
	 * @var ILogger
	 */
	public $logger;

	public function __construct(ILogger $logger)
	{
		$this->logger = $logger;
	}
}
