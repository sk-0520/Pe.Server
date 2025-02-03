<?php

declare(strict_types=1);

namespace PeServer\App\Cli;

use PeServer\Core\Cli\CliApplicationBase;
use PeServer\Core\Database\DatabaseContext;
use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\DI\Inject;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;

abstract class AppApplicationBase extends CliApplicationBase
{
	#region variable

	#[Inject] //@phpstan-ignore-next-line [INJECT]
	private IDatabaseConnection $databaseConnection;

	#endregion

	public function __construct(ILoggerFactory $loggerFactory)
	{
		parent::__construct($loggerFactory);
	}

	#region function

	/**
	 * データベース接続処理。
	 *
	 * @return DatabaseContext
	 */
	protected function openDatabase(): DatabaseContext
	{
		return $this->databaseConnection->open();
	}

	#endregion
}
