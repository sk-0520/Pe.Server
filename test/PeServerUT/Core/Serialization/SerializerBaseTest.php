<?php

declare(strict_types=1);

namespace PeServerUT\Core\Serialization;

use PeServer\Core\Binary;
use PeServer\Core\Serialization\SerializerBase;
use PeServer\Core\Throws\DeserializeException;
use PeServer\Core\Throws\ThrowableTrait;
use PeServer\Core\Throws\SerializeException;
use PeServerTest\TestClass;

class SerializerBaseTest extends TestClass
{
	public function test_save_throw()
	{
		$serializer = new LocalErrorSerializer();
		$this->expectException(SerializeException::class);

		$serializer->save([]);
		$this->fail();
	}

	public function test_load_throw()
	{
		$serializer = new LocalErrorSerializer();
		$this->expectException(DeserializeException::class);

		$serializer->load(new Binary(''));
		$this->fail();
	}
}

class LocalErrorSerializerException extends SerializeException
{
	use ThrowableTrait;
}

class LocalErrorDeserializeException extends DeserializeException
{
	use ThrowableTrait;
}

class LocalErrorSerializer extends SerializerBase
{

	protected function saveImpl(array|object $value): Binary
	{
		throw new LocalErrorSerializerException();
	}

	protected function loadImpl(Binary $value): array|object
	{
		throw new LocalErrorDeserializeException();
	}
}
