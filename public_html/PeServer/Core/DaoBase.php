<?php

declare(strict_types=1);

namespace PeServer\Core;

use \PDO;
use \PDOStatement;

use PeServer\Core\Throws\SqlException;
use PeServer\Core\Database;
use PeServer\Core\ILogger;
use PeServer\Core\Log\Logging;

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
	protected Database $database;

	/**
	 * 生成。
	 *
	 * @param Database $database 接続処理。
	 */
	protected function __construct(Database $database)
	{
		$this->logger = Logging::create(__CLASS__);
		$this->database = $database;
	}
}
