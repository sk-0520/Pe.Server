<?php

declare(strict_types=1);

namespace PeServer\Core;

use \PDO;
use \PDOStatement;
use PeServer\Core\Log\Logging;
use \PeServer\Core\Throws\SqlException;

/**
 * DB接続処理。
 */
abstract class Database
{
	/**
	 * 初期化チェック
	 *
	 * @var InitializeChecker|null
	 */
	protected static $initializeChecker;

	/**
	 * DB接続設定
	 *
	 * @var array<string,mixed>
	 */
	private static $databaseConfiguration;

	/**
	 * Undocumented function
	 *
	 * @param array<string,mixed> $databaseConfiguration
	 * @return void
	 */
	public static function initialize(array $databaseConfiguration): void
	{
		if (is_null(self::$initializeChecker)) {
			self::$initializeChecker = new InitializeChecker();
		}
		self::$initializeChecker->initialize();

		self::$databaseConfiguration = $databaseConfiguration;
	}

	public static function open(): Database
	{
		self::$initializeChecker->throwIfNotInitialize(); // @phpstan-ignore-line null access

		$logger = Logging::create('database');

		return new _Database_Impl(self::$databaseConfiguration, $logger);
	}

	/**
	 * トランザクション開始。
	 *
	 * @return void
	 * @throws \PDOException
	 * @throws SqlException
	 */
	public abstract function beginTransaction(): void;
	/**
	 * トランザクションの確定。
	 *
	 * @return void
	 * @throws \PDOException
	 * @throws SqlException
	 */
	public abstract function commit(): void;
	/**
	 * トランザクションの取消。
	 *
	 * @return void
	 * @throws \PDOException
	 * @throws SqlException
	 */
	public abstract function rollback(): void;

	/**
	 * トランザクションラップ処理。
	 *
	 * @param callable $callback 実際の処理。戻り値が真の場合にコミット、偽ならロールバック。
	 * @param mixed ...$arguments 引数
	 * @return bool コミットされたか
	 */
	public abstract function transaction(callable $callback, ...$arguments): bool;

	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int> $parameters
	 * @return mixed[]
	 */
	public abstract function query(string $statement, array $parameters = array()): array;

	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int> $parameters
	 * @return array<string,mixed>
	 */
	public abstract function queryFirst(string $statement, array $parameters = array()): array;
	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string,mixed>|mixed $defaultValue 戻り。
	 * @param array<string|int,string|int> $parameters
	 * @return array<string,mixed>|mixed
	 */
	public abstract function queryFirstOrDefault($defaultValue, string $statement, array $parameters = array());

	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int> $parameters
	 * @return integer
	 */
	public abstract function execute(string $statement, array $parameters = array()): int;

	/**
	 * 並ぶ順問い合わせ文を強制。
	 *
	 * 単純な文字列処理のため無理な時は無理。
	 *
	 * @param string $statement
	 * @param array<string|int,string|int> $parameters
	 * @return mixed[]
	 */
	public function selectOrdered(string $statement, array $parameters = array()): array
	{
		if (!preg_match('/\\border\\s+by\\b/i', $statement)) {
			throw new SqlException();
		}

		return $this->query($statement, $parameters);
	}

	/**
	 * 単一 COUNT 関数問い合わせ文を強制。
	 *
	 * 単純な文字列処理のため無理な時は無理。
	 *
	 * @param string $statement
	 * @param array<string|int,string|int> $parameters
	 * @return integer
	 */
	public function selectSingleCount(string $statement, array $parameters = array()): int
	{
		if (!preg_match('/\\bselect\\s+count\\s*\\(/i', $statement)) {
			throw new SqlException();
		}

		$result = $this->queryFirst($statement, $parameters);
		return (int)current($result);
	}

	/**
	 * INSERT文を強制。
	 *
	 * 単純な文字列処理のため無理な時は無理。
	 *
	 * @param string $statement
	 * @return void
	 */
	private function enforceInsert(string $statement): void
	{
		if (!preg_match('/\\binsert\\b/i', $statement)) {
			throw new SqlException();
		}
	}

	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int> $parameters
	 * @return integer
	 */
	public function insert(string $statement, array $parameters = array()): int
	{
		$this->enforceInsert($statement);
		return $this->execute($statement, $parameters);
	}

	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int> $parameters
	 * @return void
	 */
	public function insertSingle(string $statement, array $parameters = array()): void
	{
		$this->enforceInsert($statement);
		$result = $this->execute($statement, $parameters);
		if ($result !== 1) {
			throw new SqlException();
		}
	}

	/**
	 * UPDATE文を強制。
	 *
	 * 単純な文字列処理のため無理な時は無理。
	 *
	 * @param string $statement
	 * @return void
	 */
	private function enforceUpdate(string $statement): void
	{
		if (!preg_match('/\\bupdate\\b/i', $statement)) {
			throw new SqlException();
		}
	}

	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int> $parameters
	 * @return integer
	 */
	public function update(string $statement, array $parameters = array()): int
	{
		$this->enforceUpdate($statement);
		return $this->execute($statement, $parameters);
	}

	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int> $parameters
	 * @return void
	 */
	public function updateByKey(string $statement, array $parameters = array()): void
	{
		$this->enforceUpdate($statement);
		$result = $this->execute($statement, $parameters);
		if ($result !== 1) {
			throw new SqlException();
		}
	}
	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int> $parameters
	 * @return boolean
	 */
	public function updateByKeyOrNothing(string $statement, array $parameters = array()): bool
	{
		$this->enforceUpdate($statement);
		$result = $this->execute($statement, $parameters);
		if (1 < $result) {
			throw new SqlException();
		}

		return $result === 1;
	}

	/**
	 * DELETE文を強制。
	 *
	 * 単純な文字列処理のため無理な時は無理。
	 *
	 * @param string $statement
	 * @return void
	 */
	private function enforceDelete(string $statement): void
	{
		if (!preg_match('/\\bdelete\\b/i', $statement)) {
			throw new SqlException();
		}
	}

	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int> $parameters
	 * @return integer
	 */
	public function delete(string $statement, array $parameters = array()): int
	{
		$this->enforceDelete($statement);
		return $this->execute($statement, $parameters);
	}

	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int> $parameters
	 * @return void
	 */
	public function deleteByKey(string $statement, array $parameters = array()): void
	{
		$this->enforceDelete($statement);
		$result = $this->execute($statement, $parameters);
		if ($result !== 1) {
			throw new SqlException();
		}
	}
	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int> $parameters
	 * @return boolean
	 */
	public function deleteByKeyOrNothing(string $statement, array $parameters = array()): bool
	{
		$this->enforceDelete($statement);
		$result = $this->execute($statement, $parameters);
		if (1 < $result) {
			throw new SqlException();
		}

		return $result === 1;
	}
}

/**
 * Database内部実装。
 */
class _Database_Impl extends Database
{
	/**
	 * 接続処理。
	 */
	private PDO $pdo;

	private ILogger $logger;

	/**
	 * 生成。
	 *
	 * @param array<string,string|mixed> $databaseConfiguration
	 */
	public function __construct(array $databaseConfiguration, ILogger $logger)
	{
		Database::$initializeChecker->throwIfNotInitialize(); // @phpstan-ignore-line null access
		$this->logger = $logger;

		$dsn = 'sqlite:' . $databaseConfiguration['connection'];
		$this->pdo = new PDO($dsn);
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	}

	/**
	 * 直近のエラメッセージを取得。
	 *
	 * @return string
	 */
	private function getErrorMessage(): string
	{
		return StringUtility::dump($this->pdo->errorInfo());
	}

	public function beginTransaction(): void
	{
		if (!$this->pdo->beginTransaction()) {
			throw new SqlException($this->getErrorMessage()); // これが投げられず PDOException が投げられると思う
		}
	}

	public function commit(): void
	{
		if (!$this->pdo->commit()) {
			throw new SqlException($this->getErrorMessage());
		}
	}

	public function rollback(): void
	{
		if (!$this->pdo->rollback()) {
			throw new SqlException($this->getErrorMessage());
		}
	}

	public function transaction(callable $callback, ...$arguments): bool
	{
		try {
			$this->beginTransaction();

			$result = $callback($this, ...$arguments);
			if ($result) {
				$this->commit();
				return true;
			} else {
				$this->rollback();
			}
		} catch (\Exception $ex) {
			$this->logger->error($ex);
			$this->rollback();
		}
		return false;
	}

	/**
	 * バインド実行。
	 *
	 * @param PDOStatement $statement
	 * @param array<string|int,string|int> $parameters
	 * @return void
	 */
	private function setParameters(PDOStatement $statement, array $parameters): void
	{
		if (ArrayUtility::getCount($parameters)) {
			foreach ($parameters as $key => $value) {
				$statement->bindValue($key, $value);
			}
		}
	}

	/**
	 * 文を実行。
	 *
	 * @param string $statement
	 * @param array<string|int,string|int> $parameters
	 * @return PDOStatement
	 * @throws SqlException 実行失敗。
	 */
	private function executeStatement(string $statement, array $parameters): PDOStatement
	{
		$query = $this->pdo->prepare($statement);

		$this->setParameters($query, $parameters);

		if (!$query->execute()) {
			throw new SqlException($this->getErrorMessage());
		}

		return $query;
	}


	public function query(string $statement, array $parameters = array()): array
	{
		Database::$initializeChecker->throwIfNotInitialize(); // @phpstan-ignore-line null access

		$query = $this->executeStatement($statement, $parameters);

		$result = $query->fetchAll();
		if ($result === false) {
			throw new SqlException($this->getErrorMessage());
		}

		return $result;
	}

	public function queryFirst(string $statement, array $parameters = array()): array
	{
		Database::$initializeChecker->throwIfNotInitialize(); // @phpstan-ignore-line null access

		$query = $this->executeStatement($statement, $parameters);

		$result = $query->fetch();
		if ($result === false) {
			throw new SqlException($this->getErrorMessage());
		}

		return $result;
	}

	public function queryFirstOrDefault($defaultValue, string $statement, array $parameters = array())
	{
		Database::$initializeChecker->throwIfNotInitialize(); // @phpstan-ignore-line null access

		$query = $this->executeStatement($statement, $parameters);

		$result = $query->fetch();
		if ($result === false) {
			return $defaultValue;
		}

		return $result;
	}

	public function execute(string $statement, array $parameters = array()): int
	{
		Database::$initializeChecker->throwIfNotInitialize(); // @phpstan-ignore-line null access

		$query = $this->executeStatement($statement, $parameters);

		return $query->rowCount();
	}
}
