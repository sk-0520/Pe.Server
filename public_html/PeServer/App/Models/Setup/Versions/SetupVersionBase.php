<?php

declare(strict_types=1);

namespace PeServer\App\Models\Setup\Versions;

use PeServer\App\Models\Setup\DatabaseSetupArgument;
use PeServer\App\Models\Setup\IOSetupArgument;
use PeServer\App\Models\Setup\Versions\Version;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Regex;
use PeServer\Core\Text;
use ReflectionClass;

abstract class SetupVersionBase
{
	#region variable

	protected ILogger $logger;

	#endregion

	public function __construct(ILoggerFactory $loggerFactory)
	{
		$this->logger = $loggerFactory->createLogger($this);
	}
	#region variable

	public static function getVersion(string|object $objectOrClassName): int
	{
		$rc = new ReflectionClass($objectOrClassName);
		/** @var Version[] */
		$attrs = $rc->getAttributes(Version::class);
		$attr = $attrs[0];

		return $attr->version;
	}

	public function getCurrentVersion(): int
	{
		return self::getVersion($this);
	}

	#endregion

	#region function

	/**
	 * DB問い合わせ文の分割。
	 *
	 * @param string $statements
	 * @return string[]
	 */
	protected function splitStatements(string $statements): array
	{
		$regex = new Regex();

		//$sqlItems =  $regex->split($statements, '/^.*;.*$/');
		$sqlItems =  $regex->split($statements, '/^\s*;\s*$/m');
		$result = [];
		foreach($sqlItems as $statement) {
			if(Text::isNullOrWhiteSpace($statement)) {
				continue;
			}

			$result[] = $statement;
		}

		return $result;
	}

	protected abstract function migrateIOSystem(IOSetupArgument $argument): void;

	protected abstract function migrateDatabase(DatabaseSetupArgument $argument): void;

	public function migrate(IOSetupArgument $ioSetup, DatabaseSetupArgument $database): void
	{
		$this->migrateIOSystem($ioSetup);
		$this->migrateDatabase($database);
	}

	#endregion
}
