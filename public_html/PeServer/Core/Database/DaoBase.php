<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use PeServer\Core\Log\ILogger;
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
	 * @readonly
	 */
	protected ILogger $logger;

	/**
	 * 生成。
	 *
	 * @param IDatabaseContext $context 接続処理。
	 */
	protected function __construct(
		/** @readonly */
		protected IDatabaseContext $context
	) {
		$this->logger = Logging::create(get_class($this));
	}
}
