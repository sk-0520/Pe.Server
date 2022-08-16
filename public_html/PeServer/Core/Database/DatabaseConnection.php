<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use PeServer\Core\Database\Database;
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

	//[IDatabaseConnection]

	public function open(): Database
	{
		return new Database(
			$this->setting->dsn,
			$this->setting->user,
			$this->setting->password,
			$this->setting->options,
			$this->loggerFactory->createLogger(Database::class)
		);
	}
}
