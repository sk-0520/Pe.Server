<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Content;

use Iterator;
use PeServer\Core\Mvc\Content\EventStreamContentBase;
use PeServer\Core\Mvc\Content\IDownloadContent;
use PeServer\Core\Throws\NotSupportedException;
use PeServerTest\TestClass;

class EventStreamContentBaseTest extends TestClass
{
	public function test_constructor_throw()
	{
		// ふつーに作る分にはOK
		new class extends EventStreamContentBase {
			protected function getIterator(): Iterator
			{
				yield from [];
			}
		};

		$this->expectException(NotSupportedException::class);
		$this->expectExceptionMessage('IDownloadContent');
		// IDownloadContent を実装してると死ぬ
		new class extends EventStreamContentBase implements IDownloadContent {
			protected function getIterator(): Iterator
			{
				yield from [];
			}
			public function getFileName(): string
			{
				return "file-name";
			}
		};
	}
}
