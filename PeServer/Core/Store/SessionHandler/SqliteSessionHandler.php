<?php

declare(strict_types=1);

namespace PeServer\Core\Store\SessionHandler;

use PeServer\App\Models\Dao\Entities\SessionsEntityDao;
use PeServer\Core\Binary;
use PeServer\Core\Database\ConnectionSetting;
use PeServer\Core\Database\DatabaseConnection;
use PeServer\Core\Database\DatabaseContext;
use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\Path;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Serialization\BuiltinSerializer;
use PeServer\Core\Serialization\ISerializer;
use PeServer\Core\Serialization\JsonSerializer;
use PeServer\Core\Text;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Utc;
use SessionHandlerInterface;
use Throwable;

class SqliteSessionHandler implements SessionHandlerInterface
{
	#region define

	public const FILE_NAME = "session.sqlite3";

	#endregion

	#region variable

	private ILogger $logger;
	private DatabaseContext|null $context = null;

	#endregion

	public function __construct(private IDatabaseConnection $connection, private ILoggerFactory $loggerFactory)
	{
		$this->logger = $this->loggerFactory->createLogger($this);
	}

	#region function

	public static function createConnection(string $dir, string|null $name, ILoggerFactory $loggerFactory): IDatabaseConnection
	{
		Directory::createDirectoryIfNotExists($dir);
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
		try {
			$this->context = $this->connection->open();
			return true;
		} catch (Throwable) {
			return false;
		}
	}

	public function close(): bool
	{
		if ($this->context !== null) {
			$this->context->dispose();
			return true;
		}

		return false;
	}

	public function destroy(string $id): bool
	{
		$this->logger->debug("destroy");
		throw new NotImplementedException();
	}

	public function gc(int $max_lifetime): int|false
	{
		$this->logger->debug("gc");
		throw new NotImplementedException();
	}

	public function read(string $id): string|false
	{
		if ($this->context === null) {
			return false;
		}

		$dao = new SessionsEntityDao($this->context, $this->logger);

		$result = $dao->selectSessionDataBySessionId($id);

		if ($result === null) {
			return "";
		}

		return $result;
	}

	public function write(string $id, string $data): bool
	{
		if ($this->context === null) {
			return false;
		}

		$dao = new SessionsEntityDao($this->context, $this->logger);

		$dao->upsertSessionDataBySessionId($id, $data, Utc::create());

		return true;
	}

	#endregion
}
