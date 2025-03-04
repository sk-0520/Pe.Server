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
use PeServer\Core\Setup\MigrationArgument;
use PeServer\Core\Setup\MigrationBase;
use PeServer\Core\Text;
use PeServer\Core\Throws\NotSupportedException;
use PeServer\Core\Setup\MigrationVersion;
use ReflectionClass;

abstract class SessionSetupVersionBase extends MigrationBase
{
	public function __construct(int $version, ILoggerFactory $loggerFactory)
	{
		parent::__construct($version, $loggerFactory);
	}

	#region function

	abstract protected function migrateDatabase(MigrationArgument $argument): void;

	#endregion

	#region MigrationBase

	final public function migrate(MigrationArgument $argument): void
	{
		$this->migrateDatabase($argument);
	}

	#endregion
}
