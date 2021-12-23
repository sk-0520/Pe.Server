<?php

declare(strict_types=1);

namespace PeServer\Core;

use \PDO;
use \PDOStatement;

use \PeServer\Core\Throws\SqlException;
use \PeServer\Core\Database;
use \PeServer\Core\ILogger;
use \PeServer\Core\Log\Logging;

abstract class DaoBase
{
	/**
	 * Undocumented variable
	 *
	 * @var ILogger
	 */
	protected $logger;
	/**
	 * Undocumented variable
	 *
	 * @var Database
	 */
	protected $database;

	protected function __construct(Database $database)
	{
		$this->logger = Logging::create(__CLASS__);
		$this->database = $database;
	}
}
