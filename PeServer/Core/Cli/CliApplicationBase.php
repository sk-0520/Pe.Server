<?php

declare(strict_types=1);

namespace PeServer\Core\Cli;

use Exception;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Text;
use PeServer\Core\TypeUtility;

abstract class CliApplicationBase
{
	#region variable

	protected ILogger $logger;
	public int $exitCode = 0;

	#endregion

	public function __construct(protected ILoggerFactory $loggerFactory)
	{
		$this->logger = $loggerFactory->createLogger($this);
	}

	#region function

	abstract protected function executeImpl(): void;

	public function execute(): void
	{
		$classFullName = TypeUtility::getType($this);
		$lastIndex = Text::getLastPosition($classFullName, "\\");
		$className = Text::substring($classFullName, $lastIndex + 1);

		$this->logger->info("<{0}> start", $className);
		try {
			$this->executeImpl();
			$this->logger->info("<{0}> end", $className);
		} catch (Exception $ex) {
			$this->logger->error("<{0}> error, {1}", $className,$ex);
		}
	}

	#endregion
}
