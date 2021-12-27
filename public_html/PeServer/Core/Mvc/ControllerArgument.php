<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use \PeServer\Core\ILogger;

/**
 * コントローラ生成時に使用される入力値。
 */
class ControllerArgument
{
	/**
	 * ロガー
	 *
	 * @var ILogger
	 */
	public $logger;

	public SessionStore $session;

	public function __construct(SessionStore $session, ILogger $logger)
	{
		$this->session = $session;
		$this->logger = $logger;
	}
}
