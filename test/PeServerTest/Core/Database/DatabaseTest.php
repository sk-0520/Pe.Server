<?php

declare(strict_types=1);

namespace PeServerTest\Core\Database;

use PeServer\Core\Archiver;
use PeServer\Core\Binary;
use PeServer\Core\Database\Database;
use PeServer\Core\Log\Logging;
use PeServer\Core\Throws\DatabaseException;
use PeServerTest\Core\Database\DB;
use PeServerTest\TestClass;

class DatabaseTest extends TestClass
{
	function test_constructor()
	{
		DB::memory();
		$this->success();
	}

	function test_constructor_throw()
	{
		$this->expectException(DatabaseException::class);
		new Database('', '', '', null, Logging::create(get_class($this)));
		$this->fail();
	}
}
