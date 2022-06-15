<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use PeServer\Core\ILogger;
use PeServer\Core\Log\Logging;
use PeServer\Core\Database\IDatabaseContext;

/**
 * DBアクセス基底処理。
 *
 * こいつを継承してアクセス処理を構築する。
 */
abstract class DaoBase
{
	/**
	 * ロガー。
	 */
	protected ILogger $logger;
	/**
	 * 接続処理。
	 */
	protected IDatabaseContext $context;

	/**
	 * 生成。
	 *
	 * @param IDatabaseContext $context 接続処理。
	 */
	protected function __construct(IDatabaseContext $context)
	{
		$this->logger = Logging::create(get_class($this));
		$this->context = $context;
	}
}
