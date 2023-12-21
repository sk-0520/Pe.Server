<?php

declare(strict_types=1);

namespace PeServerTest;

use PeServer\Core\Binary;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\IO\Stream;

class TestDirectory
{
	public function __construct(
		public string $path
	) {
	}

	public function newPath(string $name): string
	{
		return Path::combine($this->path, $name);
	}

	public function createDirectory(string $name): string
	{
		$path = $this->newPath($name);
		Directory::createDirectory($path);

		return $path;
	}

	public function createFile(string $name, ?Binary $data = null): string
	{
		$path = $this->newPath($name);

		if ($data) {
			File::writeContent($path, $data);
		} else {
			File::createEmptyFileIfNotExists($path);
		}

		return $path;
	}

	public function createStream(string $name): Stream
	{
		$path = $this->newPath($name);
		return Stream::create($path);
	}
}
