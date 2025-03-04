<?php

declare(strict_types=1);

namespace PeServer\Core\Setup;

use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Regex;
use PeServer\Core\Text;

abstract class MigrationBase
{
	#region variable

	protected ILogger $logger;

	#endregion

	public function __construct(protected int $version, protected ILoggerFactory $loggerFactory)
	{
		$this->logger = $loggerFactory->createLogger($this);
	}

	#region function

	// public function getCurrentVersion(): int
	// {
	// 	return self::getVersion($this);
	// }

	/**
	 * DB問い合わせ文の分割。
	 *
	 * @param string $multiStatement
	 * @return string[]
	 * @phpstan-return literal-string[]
	 */
	protected function splitStatements(string $multiStatement): array
	{
		$regex = new Regex();

		$statements =  $regex->split($multiStatement, '/^\s*;\s*$/m');
		/** @phpstan-var literal-string[] */
		$result = [];
		foreach ($statements as $statement) {
			if (Text::isNullOrWhiteSpace($statement)) {
				continue;
			}

			$result[] = $statement;
		}

		/** @phpstan-var literal-string[] $result */
		return $result;
	}

	public abstract function migrate(MigrationArgument $argument): void;

	#endregion
}
