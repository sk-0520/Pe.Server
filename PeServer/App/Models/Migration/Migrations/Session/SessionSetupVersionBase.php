<?php

declare(strict_types=1);

namespace PeServer\App\Models\Migration\Migrations\Session;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Setup\DatabaseSetupArgument;
use PeServer\App\Models\Setup\IOSetupArgument;
use PeServer\App\Models\Setup\Versions\SetupVersionBase;
use PeServer\App\Models\Setup\Versions\Version;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Regex;
use PeServer\Core\Text;
use PeServer\Core\Throws\NotSupportedException;
use PeServer\Core\Setup\MigrationVersion;
use ReflectionClass;

abstract class SessionSetupVersionBase extends SetupVersionBase
{
	public function __construct(protected AppConfiguration $appConfig, ILoggerFactory $loggerFactory)
	{
		parent::__construct($appConfig, $loggerFactory);
	}

	#region variable

	final protected function migrateIOSystem(IOSetupArgument $argument): void
	{
		throw new NotSupportedException();
	}

	abstract protected function migrateDatabase(DatabaseSetupArgument $argument): void;

	final public function migrate(IOSetupArgument $ioSetup, DatabaseSetupArgument $database): void
	{
		$this->migrateDatabase($database);
	}

	#endregion
}
