<?php

declare(strict_types=1);

namespace PeServer\Core\Store\SessionHandler;

use PeServer\Core\Database\ConnectionSetting;
use PeServer\Core\Database\DatabaseConnection;
use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\IO\Path;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Text;
use PeServer\Core\Throws\NotImplementedException;
use SessionHandlerInterface;

class SqliteSessionHandler implements SessionHandlerInterface
{
	#region define

	const FILE_NAME = "session.sqlite3";

	#endregion

	#region variable

	private ILogger $logger;

	#endregion

	public function __construct(private IDatabaseConnection $connection, private ILoggerFactory $loggerFactory)
	{
		$this->logger = $loggerFactory->createLogger($this);
	}

	#region function

	public static function createConnection(string $dir, string|null $name, ILoggerFactory $loggerFactory): IDatabaseConnection
	{
		$sessionPath = Path::combine($dir, Text::ensureIfNotNullOrWhiteSpace($name, self::FILE_NAME));
		$connectionSetting = new ConnectionSetting(
			"sqlite:{$sessionPath}",
			"",
			""
		);
		return new DatabaseConnection($connectionSetting, $loggerFactory);
	}

	#endregion

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
