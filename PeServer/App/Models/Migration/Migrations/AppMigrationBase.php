<?php

declare(strict_types=1);

namespace PeServer\App\Models\Migration\Migrations;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Setup\DatabaseSetupArgument;
use PeServer\App\Models\Setup\IOSetupArgument;
use PeServer\App\Models\Setup\Versions\SetupVersionBase;
use PeServer\App\Models\Setup\Versions\Version;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Regex;
use PeServer\Core\Migration\MigrationArgument;
use PeServer\Core\Migration\MigrationBase;
use PeServer\Core\Text;
use PeServer\Core\Throws\NotSupportedException;
use PeServer\Core\Migration\MigrationVersion;
use ReflectionClass;

abstract class AppMigrationBase extends MigrationBase
{
	public function __construct(int $version, ILoggerFactory $loggerFactory)
	{
		parent::__construct($version, $loggerFactory);
	}
}
