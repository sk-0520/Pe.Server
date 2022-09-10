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

	/**
	 * [汎用] バージョン取得
	 *
	 * @template T of object
	 * @param string|object $objectOrClassName
	 * @phpstan-param class-string<T>|T $objectOrClassName
	 * @return int
	 */
	public static function getVersion(string|object $objectOrClassName): int
	{
		$rc = new ReflectionClass($objectOrClassName);
		$attrs = $rc->getAttributes(Version::class);
		$attr = $attrs[0];

		/** @var Version */
		$version = $attr->newInstance();

		return $version->version;
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
		foreach($statements as $statement) {
			if(Text::isNullOrWhiteSpace($statement)) {
				continue;
			}
			/** @phpstan-var literal-string $statement*/

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
