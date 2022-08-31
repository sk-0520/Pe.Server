<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use PeServer\Core\Database\DatabaseContext;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Throws\NotImplementedException;

class DatabaseConnection implements IDatabaseConnection
{
	/**
	 * 生成。
	 *
	 * @param ConnectionSetting $setting 接続設定。
	 */
	public function __construct(
		/** @readonly */
		protected ConnectionSetting $setting,
		protected ILoggerFactory $loggerFactory
	) {
	}

	#region IDatabaseConnection

	public function open(): DatabaseContext
	{
		return new DatabaseContext(
			$this->setting,
			$this->loggerFactory->createLogger(DatabaseContext::class)
		);
	}

	#endregion
}
