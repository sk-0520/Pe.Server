<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Content;

use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Content\DownloadDataContent;
use PeServerTest\TestClass;

class DownloadDataContentTest extends TestClass
{
	#region function

	public function test_constructor()
	{
		$actual = new DownloadDataContent("MIME", "FILE-NAME", "DATA");
		$this->assertSame(HttpStatus::OK, $actual->httpStatus);
		$this->assertSame("MIME", $actual->mime);
		$this->assertSame("FILE-NAME", $actual->getFileName());
		$this->assertSame("DATA", $actual->data);

	}

	#endregion
}
