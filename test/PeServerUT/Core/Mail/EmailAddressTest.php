<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mail;

use PeServer\Core\Mail\EmailAddress;
use PeServerTest\Data;
use PeServerTest\TestClass;

class EmailAddressTest extends TestClass
{
	public function test_construct()
	{
		$tests = [
			new Data(new EmailAddress('', ''), '', ''),
			new Data(new EmailAddress('addr', 'name'), 'addr', 'name'),
			new Data(new EmailAddress('addr'), 'addr', ''),
			new Data(new EmailAddress('addr'), 'addr', null),
		];
		foreach ($tests as $test) {
			$actual = new EmailAddress(...$test->args);
			$this->assertSame($test->expected->address, $actual->address);
			$this->assertSame($test->expected->name, $actual->name);
		}
	}
}
