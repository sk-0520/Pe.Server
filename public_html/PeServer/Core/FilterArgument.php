<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Exception;
use \LogicException;
use \PeServer\Core\Mvc\SessionStore;

/**
 * フィルタリング時の入力パラメータ。
 */
class FilterArgument
{
	public SessionStore $session;

	public function __construct(SessionStore $session)
	{
		$this->session = $session;
	}
}
