SessionIdInterface
<?php

declare(strict_types=1);

namespace PeServer\Core\Store\SessionHandler;

use PeServer\Core\Throws\NotImplementedException;
use SessionHandlerInterface;

class SqliteSessionHandler implements SessionHandlerInterface
{
	#region SessionHandlerInterface

	public function open(string $path, string $name): bool
	{
		throw new NotImplementedException();
	}

	public function close(): bool
	{
		throw new NotImplementedException();
	}

	public function destroy(string $id): bool
	{
		throw new NotImplementedException();
	}

	public function gc(int $max_lifetime): int|false
	{
		throw new NotImplementedException();
	}

	public function read(string $id): string|false
	{
		throw new NotImplementedException();
	}

	public function write(string $id, string $data): bool
	{
		throw new NotImplementedException();
	}

	#endregion
}
