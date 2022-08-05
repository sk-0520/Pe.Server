<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use \PDO;
use \PDOStatement;
use \Exception;
use PDOException;
use PeServer\Core\Database\IDatabaseTransactionContext;
use PeServer\Core\DisposerBase;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Regex;
use PeServer\Core\StringUtility;
use PeServer\Core\Throws\DatabaseException;
use PeServer\Core\Throws\SqlException;
use PeServer\Core\Throws\Throws;
use PeServer\Core\Throws\TransactionException;
use PeServer\Core\TypeUtility;

/**
 * DB接続処理。
 */
class Database extends DisposerBase implements IDatabaseTransactionContext
{
	/**
	 * 接続処理。
	 *
	 * @readonly
	 */
	protected PDO $pdo;

	/**
	 * ロガー
	 *
	 * @var ILogger
	 * @readonly
	 */
	protected ILogger $logger;

	/**
	 * 生成。
	 *
	 * @param string $dsn
	 * @param string $user
	 * @param string $password
	 * @param array<string,string>|null $options
	 * @param ILogger $logger
	 * @throws DatabaseException
	 */
	public function __construct(string $dsn, string $user, string $password, ?array $options, ILogger $logger)
	{
		$this->logger = $logger;

		$this->pdo = Throws::wrap(PDOException::class, DatabaseException::class, fn () => new PDO($dsn, $user, $password, $options));
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	}

	protected function disposeImpl(): void
	{
		if ($this->inTransaction()) {
			$this->rollback();
		}

		parent::disposeImpl();
	}

	/**
	 * 直近のエラーメッセージを取得。
	 *
	 * @return string
	 */
	private function getErrorMessage(): string
	{
		return StringUtility::dump($this->pdo->errorInfo());
	}

	/**
	 * バインド実行。
	 *
	 * @param PDOStatement $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @phpstan-param array<array-key,DatabaseBindValueAlias>|null $parameters
	 * @return void
	 */
	private function setParameters(PDOStatement $statement, ?array $parameters): void
	{
		if (!is_null($parameters)) {
			foreach ($parameters as $key => $value) {
				$statement->bindValue($key, $value);
			}
		}
	}

	/**
	 * 文を実行。
	 *
	 * @param string $statement
	 * @phpstan-param array<array-key,DatabaseBindValueAlias>|null $parameters
	 * @return PDOStatement
	 * @throws SqlException 実行失敗。
	 */
	private function executeStatement(string $statement, ?array $parameters): PDOStatement
	{
		$this->throwIfDisposed();

		$query = $this->pdo->prepare($statement);

		$this->setParameters($query, $parameters);

		$this->logger->trace($query, $parameters);

		if (!$query->execute()) {
			throw new SqlException($this->getErrorMessage());
		}

		return $query;
	}

	public function inTransaction(): bool
	{
		$this->throwIfDisposed();

		return $this->pdo->inTransaction();
	}

	public function beginTransaction(): void
	{
		$this->throwIfDisposed();

		if ($this->inTransaction()) {
			throw new TransactionException();
		}

		if (!$this->pdo->beginTransaction()) {
			throw new SqlException($this->getErrorMessage()); // これが投げられず PDOException が投げられると思う
		}
	}

	public function commit(): void
	{
		$this->throwIfDisposed();

		if (!$this->inTransaction()) {
			throw new TransactionException();
		}

		if (!$this->pdo->commit()) {
			throw new SqlException($this->getErrorMessage());
		}
	}

	public function rollback(): void
	{
		if (!$this->inTransaction()) {
			throw new TransactionException();
		}

		if (!$this->pdo->rollBack()) {
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
		} catch (Exception $ex) {
			$this->logger->error($ex);
			$this->rollback();
			Throws::reThrow(SqlException::class, $ex);
		}

		return false;
	}

	public function query(string $statement, ?array $parameters = null): array
	{
		$query = $this->executeStatement($statement, $parameters);

		$result = $query->fetchAll();
		// @phpstan-ignore-next-line: Strict comparison using === between
		if ($result === false) {
			throw new DatabaseException($this->getErrorMessage());
		}

		return $result;
	}

	public function queryFirst(string $statement, ?array $parameters = null): array
	{
		$query = $this->executeStatement($statement, $parameters);

		/** @var array<string,mixed>|false */
		$result = $query->fetch();
		if ($result === false) {
			throw new DatabaseException($this->getErrorMessage());
		}

		return $result;
	}

	public function queryFirstOr(?array $defaultValue, string $statement, ?array $parameters = null): ?array
	{
		$query = $this->executeStatement($statement, $parameters);

		/** @var array<string,mixed>|false */
		$result = $query->fetch();
		if ($result === false) {
			return $defaultValue;
		}

		return $result;
	}

	public function querySingle(string $statement, ?array $parameters = null): array
	{
		$query = $this->executeStatement($statement, $parameters);

		/** @var array<string,mixed>|false */
		$result = $query->fetch();
		if ($result === false) {
			throw new DatabaseException($this->getErrorMessage());
		}

		$next = $query->fetch();
		if ($next !== false) {
			throw new DatabaseException($this->getErrorMessage());
		}

		return $result;
	}

	public function querySingleOr(?array $defaultValue, string $statement, ?array $parameters = null): ?array
	{
		$query = $this->executeStatement($statement, $parameters);

		/** @var array<string,mixed>|false */
		$result = $query->fetch();
		if ($result === false) {
			return $defaultValue;
		}

		$next = $query->fetch();
		if ($next !== false) {
			throw new DatabaseException($this->getErrorMessage());
		}

		return $result;
	}

	public function selectOrdered(string $statement, ?array $parameters = null): array
	{
		if (!Regex::isMatch($statement, '/\\border\\s+by\\b/i')) {
			throw new SqlException();
		}

		return $this->query($statement, $parameters);
	}

	public function selectSingleCount(string $statement, ?array $parameters = null): int
	{
		if (!Regex::isMatch($statement, '/\\bselect\\s+count\\s*\\(/i')) {
			throw new SqlException();
		}

		/** @var array<string,mixed> */
		$result = $this->queryFirst($statement, $parameters);
		$val = strval(current($result));
		if (TypeUtility::tryParseInteger($val, $count)) {
			return $count;
		}

		throw new DatabaseException();
	}

	public function execute(string $statement, ?array $parameters = null): int
	{
		$query = $this->executeStatement($statement, $parameters);

		return $query->rowCount();
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
		if (!Regex::isMatch($statement, '/\\binsert\\b/i')) {
			throw new SqlException();
		}
	}

	public function insert(string $statement, ?array $parameters = null): int
	{
		$this->enforceInsert($statement);
		return $this->execute($statement, $parameters);
	}

	public function insertSingle(string $statement, ?array $parameters = null): void
	{
		$this->enforceInsert($statement);
		$result = $this->execute($statement, $parameters);
		if ($result !== 1) {
			throw new DatabaseException();
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
		if (!Regex::isMatch($statement, '/\\bupdate\\b/i')) {
			throw new SqlException();
		}
	}

	public function update(string $statement, ?array $parameters = null): int
	{
		$this->enforceUpdate($statement);
		return $this->execute($statement, $parameters);
	}

	public function updateByKey(string $statement, ?array $parameters = null): void
	{
		$this->enforceUpdate($statement);
		$result = $this->execute($statement, $parameters);
		if ($result !== 1) {
			throw new SqlException();
		}
	}

	public function updateByKeyOrNothing(string $statement, ?array $parameters = null): bool
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
		if (!Regex::isMatch($statement, '/\\bdelete\\b/i')) {
			throw new SqlException();
		}
	}

	public function delete(string $statement, ?array $parameters = null): int
	{
		$this->enforceDelete($statement);
		return $this->execute($statement, $parameters);
	}

	public function deleteByKey(string $statement, ?array $parameters = null): void
	{
		$this->enforceDelete($statement);
		$result = $this->execute($statement, $parameters);
		if ($result !== 1) {
			throw new SqlException();
		}
	}

	public function deleteByKeyOrNothing(string $statement, ?array $parameters = null): bool
	{
		$this->enforceDelete($statement);
		$result = $this->execute($statement, $parameters);
		if (1 < $result) {
			throw new SqlException();
		}

		return $result === 1;
	}
}
