<?php

declare(strict_types=1);

namespace PeServer\App\Models\Setup\Versions;

use PeServer\App\Models\Setup\DatabaseSetupArgument;
use PeServer\App\Models\Setup\IOSetupArgument;
use PeServer\App\Models\Setup\Versions\Version;
use PeServer\Core\Database\IDatabaseContext;
use ReflectionClass;

abstract class SetupVersionBase
{
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

	protected abstract function migrateIOSystem(IOSetupArgument $argument): void;

	protected abstract function migrateDatabase(DatabaseSetupArgument $argument): void;

	public function migrate(IOSetupArgument $ioSetup, DatabaseSetupArgument $database): void
	{
		$this->migrateIOSystem($ioSetup);
		$this->migrateDatabase($database);
	}

	#endregion
}
