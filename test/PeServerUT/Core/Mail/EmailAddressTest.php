<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mail;

use PeServer\Core\Mail\EmailAddress;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class EmailAddressTest extends TestClass
{
	public static function provider_construct()
	{
		return [
			[new EmailAddress('', ''), '', ''],
			[new EmailAddress('addr', 'name'), 'addr', 'name'],
			[new EmailAddress('addr'), 'addr', ''],
			[new EmailAddress('addr'), 'addr', null],
		];
	}

	#[DataProvider('provider_construct')]
	public function test_construct(EmailAddress $expected, string $address, ?string $name = null)
	{
		$actual = new EmailAddress($address, $name);
		$this->assertSame($expected->address, $actual->address);
		$this->assertSame($expected->name, $actual->name);
	}
}
