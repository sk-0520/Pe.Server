<?php

declare(strict_types=1);

namespace PeServer\Core\Cli;

use DateTimeImmutable;
use Exception;
use PeServer\Core\DI\Inject;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Log\RamLogger;
use PeServer\Core\Log\StaticRamLogger;
use PeServer\Core\Mail\Mailer;
use PeServer\Core\Text;
use PeServer\Core\TypeUtility;
use PeServer\Core\Utc;
use Throwable;

abstract class CliApplicationBase
{
	#region variable

	protected ILogger $logger;
	public int $exitCode = 0;

	/**
	 * CLI アプリケーション開始時間。
	 */
	protected readonly DateTimeImmutable $beginTimestamp;

	#endregion

	public function __construct(protected ILoggerFactory $loggerFactory)
	{
		$this->beginTimestamp = Utc::create();

		$this->logger = $loggerFactory->createLogger($this);
	}

	#region function

	abstract protected function executeImpl(): void;

	public function execute(): void
	{
		$className = TypeUtility::getSimpleClassName($this);

		$this->logger->info("<{0}> start", $className);
		try {
			$this->executeImpl();
			$this->logger->info("<{0}> end", $className);
			$this->success();
		} catch (Throwable $ex) {
			$this->logger->error("<{0}> error, {1}", $className, $ex);
			$this->failure();
		}
	}

	protected function success(): void
	{
		//NOP
	}

	protected function failure(): void
	{
		//NOP
	}

	#endregion
}
