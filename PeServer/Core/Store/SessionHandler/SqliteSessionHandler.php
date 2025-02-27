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
use PeServer\Core\Time;
use PeServer\Core\Utc;
use SessionHandlerInterface;
use SessionUpdateTimestampHandlerInterface;
use Throwable;

class SqliteSessionHandler implements SessionHandlerInterface, SessionUpdateTimestampHandlerInterface
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

	/**
	 * DB接続の生成。
	 *
	 * @param string $dir ディレクトリパス。
	 * @param string|null $name ファイル名。空の場合は `self::FILE_NAME` が使用される。
	 * @param ILoggerFactory $loggerFactory
	 * @return IDatabaseConnection
	 */
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
		} catch (Throwable $ex) {
			$this->logger->error($ex);
		}

		return false;
	}

	public function close(): bool
	{
		if ($this->context === null) {
			return false;
		}

		$this->context->dispose();
		return true;
	}

	public function read(string $id): string|false
	{
		if ($this->context === null) {
			return false;
		}

		try {
			$dao = new SessionsEntityDao($this->context, $this->logger);

			$result = $dao->selectSessionDataBySessionId($id);

			if ($result === null) {
				return "";
			}

			return $result;
		} catch (Throwable $ex) {
			$this->logger->error($ex);
		}

		return false;
	}

	public function write(string $id, string $data): bool
	{
		if ($this->context === null) {
			return false;
		}

		try {
			$dao = new SessionsEntityDao($this->context, $this->logger);

			$dao->upsertSessionDataBySessionId($id, $data, Utc::create());

			return true;
		} catch (Throwable $ex) {
			$this->logger->error($ex);
		}

		return false;
	}

	public function destroy(string $id): bool
	{
		if ($this->context === null) {
			return false;
		}

		try {
			$dao = new SessionsEntityDao($this->context, $this->logger);

			$dao->deleteSessionBySessionId($id);

			return true;
		} catch (Throwable $ex) {
			$this->logger->error($ex);
		}

		return false;
	}

	public function gc(int $max_lifetime): int|false
	{
		if ($this->context === null) {
			return false;
		}

		$now = Utc::create();
		$safeTime = $now->sub(Time::createFromSeconds($max_lifetime));

		try {
			$dao = new SessionsEntityDao($this->context, $this->logger);
			return $dao->deleteOldSessions($safeTime);
		} catch (Throwable $ex) {
			$this->logger->error($ex);
		}

		return false;
	}


	#endregion

	#region SessionUpdateTimestampHandlerInterface

	public function validateId(string $id): bool
	{
		if ($this->context === null) {
			return false;
		}

		try {
			$dao = new SessionsEntityDao($this->context, $this->logger);
			return $dao->selectExistsBySessionId($id);
		} catch (Throwable $ex) {
			$this->logger->error($ex);
		}

		return false;
	}

	public function updateTimestamp(string $id, string $data): bool
	{
		if ($this->context === null) {
			return false;
		}

		try {
			$dao = new SessionsEntityDao($this->context, $this->logger);
			return $dao->updateSessionBySessionId($id, $data, Utc::create());
		} catch (Throwable $ex) {
			$this->logger->error($ex);
		}

		return false;
	}

	#endregion
}
