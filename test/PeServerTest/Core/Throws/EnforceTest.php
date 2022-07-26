<?php

declare(strict_types=1);

namespace PeServerTest\Core\Throws;

use \Error;
use PeServerTest\TestClass;
use PeServer\Core\Throws\Enforce;
use PeServer\Core\Throws\EnforceException;

class EnforceTest extends TestClass
{
	public function test_throwIf_true()
	{
		Enforce::throwIf(true);
		$this->success();
	}

	public function test_throwIf_false_ee()
	{
		$this->expectException(EnforceException::class);
		Enforce::throwIf(false);
		$this->fail();
	}

	public function test_throwIf_false_error()
	{
		$this->expectException(Error::class);
		Enforce::throwIf(false, '', Error::class);
		$this->fail();
	}

	public function test_throwIfNull()
	{
		$this->expectException(EnforceException::class);
		Enforce::throwIfNull(null);
		$this->fail();

		Enforce::throwIfNull(123);
		$this->success();
	}

	public function test_throwIfNullOrEmpty()
	{
		$this->expectException(EnforceException::class);
		Enforce::throwIfNullOrEmpty(null);
		$this->fail();
		Enforce::throwIfNullOrEmpty('');
		$this->fail();

		Enforce::throwIfNull(' ');
		$this->success();
	}

	public function test_throwIfNullOrWhiteSpace()
	{
		$this->expectException(EnforceException::class);
		Enforce::throwIfNullOrWhiteSpace(null);
		$this->fail();
		Enforce::throwIfNullOrWhiteSpace('');
		$this->fail();
		Enforce::throwIfNullOrWhiteSpace(' ');
		$this->fail();

		Enforce::throwIfNullOrWhiteSpace(' a ');
		$this->success();
	}
}
