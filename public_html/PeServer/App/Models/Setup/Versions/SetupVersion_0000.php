<?php

declare(strict_types=1);

namespace PeServer\App\Models\Setup\Versions;

use PeServer\App\Models\Setup\DatabaseSetupArgument;
use PeServer\App\Models\Setup\IOSetupArgument;

#[Version(0)]
class SetupVersion_0000 extends SetupVersionBase
{
	#region SetupVersionBase

	protected function migrateIOSystem(IOSetupArgument $argument): void
	{
		//NONE
	}

	protected function migrateDatabase(DatabaseSetupArgument $argument): void
	{
		//NONE
	}

	#endregion
}
