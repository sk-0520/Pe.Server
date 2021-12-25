<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use \PeServer\Core\ILogger;
use \PeServer\Core\ActionOptions;
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

	public ActionOptions $options;

	public function __construct(ActionRequest $request, ActionOptions $options, ILogger $logger)
	{
		$this->request = $request;
		$this->options = $options;
		$this->logger = $logger;
	}
}
