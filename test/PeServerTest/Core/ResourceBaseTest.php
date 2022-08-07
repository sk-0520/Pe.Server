<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use PeServer\Core\IOUtility;
use PeServer\Core\PathUtility;
use \stdClass;
use \TypeError;
use PeServer\Core\ResourceBase;
use PeServer\Core\SizeConverter;
use PeServer\Core\Stream;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\IOException;
use PeServerTest\Data;
use PeServerTest\TestClass;

class ResourceBaseTest extends TestClass
{
	public function provider_constructor_type_throw()
	{
		return [
			[0],
			[0.5],
			['abc'],
			[new stdClass()],
			[[]],
			[[1, 2, 3]],
			[['A' => 'B']],
			[false],
			[null],
		];
	}

	/** @dataProvider provider_constructor_type_throw */
	public function test_constructor_type_throw($resource)
	{
		$this->expectException(TypeError::class);
		new class($resource) extends ResourceBase
		{
			public function __construct($resource)
			{
				parent::__construct($resource);
			}

			protected function release(): void
			{
				//NONE
			}

			protected function isValidType(string $resourceType): bool
			{
				return true;
			}
		};
		$this->fail();
	}

	public function test_constructor_closed_throw()
	{
		$this->expectException(ArgumentException::class);
		$f = IOUtility::createTemporaryFilePath();
		$resource = fopen($f, 'w');
		fclose($resource);
		try {
			new class($resource) extends ResourceBase
			{
				public function __construct($resource)
				{
					parent::__construct($resource);
				}

				protected function release(): void
				{
					//NONE
				}

				protected function isValidType(string $resourceType): bool
				{
					return true;
				}
			};
		} finally {
			if (IOUtility::existsFile($f)) {
				IOUtility::removeFile($f);
			}
		}
		$this->fail();
	}
}
