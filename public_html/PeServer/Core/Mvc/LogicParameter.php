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

	public SessionStore $session;

	public ActionOptions $options;

	public function __construct(ActionRequest $request, SessionStore $session, ActionOptions $options, ILogger $logger)
	{
		$this->request = $request;
		$this->session = $session;
		$this->options = $options;
		$this->logger = $logger;
	}
}
