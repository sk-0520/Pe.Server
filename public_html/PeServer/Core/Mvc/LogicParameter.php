<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use \PeServer\Core\ILogger;
use \PeServer\Core\ActionRequest;

/**
 * ロジック用パラメータ。
 */
class LogicParameter
{
	/**
	 * ロガー
	 *
	 * @var ILogger
	 */
	public $logger;
	/**
	 * リクエスト。
	 *
	 * @var ActionRequest
	 */
	public $request;

	public function __construct(ActionRequest $request, ILogger $logger)
	{
		$this->request = $request;
		$this->logger = $logger;
	}
}
