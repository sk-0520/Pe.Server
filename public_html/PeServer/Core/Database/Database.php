<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use \PDO;
use \PDOStatement;
use \Exception;
use PDOException;
use PeServer\Core\ArrayUtility;
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
use Throwable;

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
				if (!$statement->bindValue($key, $value)) {
					throw new SqlException('$key: ' . $key . ' -> ' . TypeUtility::getType($value));
				}
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

		/** @var PDOStatement|false */
		$query = null;

		try {
			$query = $this->pdo->prepare($statement);
			if ($query === false) {
				throw new SqlException($this->getErrorMessage());
			}

			$this->setParameters($query, $parameters);
		} catch (PDOException $ex) {
			Throws::reThrow(SqlException::class, $ex);
		}

		$this->logger->trace($query, $parameters);

		try {
			if (!$query->execute()) {
				throw new DatabaseException($this->getErrorMessage());
			}
		} catch (PDOException $ex) {
			Throws::reThrow(DatabaseException::class, $ex);
		}

		return $query;
	}

	/**
	 * カラム情報一覧の取得。
	 *
	 * カラム情報は取得できたものだけを返す。
	 *
	 * @param PDOStatement $pdoStatement
	 * @return DatabaseColumn[] 取得できたカラム一覧。
	 */
	private function getColumns(PDOStatement $pdoStatement): array
	{
		$count = $pdoStatement->columnCount();
		if ($count === 0) {
			return [];
		}

		$columns = [];

		for ($i = 0; $i < $count; $i++) {
			$meta = $pdoStatement->getColumnMeta($i);
			if ($meta === false) {
				continue;
			}

			$column = DatabaseColumn::create($meta);
			$columns[] = $column;
		}

		return $columns;
	}

	/**
	 * 単一行データに変換。
	 *
	 * データが存在しない場合、`DatabaseRowResult->field` はから配列となるが、あくまで `Database` 内限定のデータ状態となる。
	 *
	 * @template TFieldArray of FieldArrayAlias
	 * @param PDOStatement $pdoStatement
	 * @return DatabaseRowResult
	 * @phpstan-return DatabaseRowResult<TFieldArray>
	 */
	private function convertRowResult(PDOStatement $pdoStatement): DatabaseRowResult
	{
		$columns = $this->getColumns($pdoStatement);

		$resultCount = $pdoStatement->rowCount();

		/** @phpstan-var TFieldArray|false */
		$row = $pdoStatement->fetch();
		if ($row === false) {
			return new DatabaseRowResult($columns, $resultCount, []); //@phpstan-ignore-line 空データを本クラス内のみ許容
		}

		return new DatabaseRowResult($columns, $resultCount, $row); //@phpstan-ignore-line 空じゃないでしょ・・・
	}

	/**
	 * データセットに変換。
	 *
	 * @template TFieldArray of FieldArrayAlias
	 * @param PDOStatement $pdoStatement
	 * @return DatabaseTableResult
	 * @phpstan-return DatabaseTableResult<TFieldArray>
	 */
	private function convertTableResult(PDOStatement $pdoStatement): DatabaseTableResult
	{
		$columns = $this->getColumns($pdoStatement);

		$resultCount = $pdoStatement->rowCount();

		$rows = $pdoStatement->fetchAll();
		// @phpstan-ignore-next-line: Strict comparison using === between
		if ($rows === false) {
			throw new DatabaseException($this->getErrorMessage());
		}

		$result = new DatabaseTableResult($columns, $resultCount, $rows);

		return $result; //@phpstan-ignore-line 空フィールドはない
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

		try {
			if (!$this->pdo->beginTransaction()) {
				throw new TransactionException($this->getErrorMessage());
			}
		} catch (PDOException $ex) {
			Throws::reThrow(TransactionException::class, $ex, $this->getErrorMessage());
		}
	}

	public function commit(): void
	{
		$this->throwIfDisposed();

		if (!$this->inTransaction()) {
			throw new TransactionException();
		}

		try {
			if (!$this->pdo->commit()) {
				throw new TransactionException($this->getErrorMessage());
			}
		} catch (PDOException $ex) {
			Throws::reThrow(TransactionException::class, $ex, $this->getErrorMessage());
		}
	}

	public function rollback(): void
	{
		if (!$this->inTransaction()) {
			throw new TransactionException();
		}

		try {
			if (!$this->pdo->rollBack()) {
				throw new TransactionException($this->getErrorMessage());
			}
		} catch (PDOException $ex) {
			Throws::reThrow(TransactionException::class, $ex, $this->getErrorMessage());
		}
	}

	public function transaction(callable $callback): bool
	{
		try {
			$this->beginTransaction();

			$result = $callback($this);
			if ($result) {
				$this->commit();
				return true;
			} else {
				$this->rollback();
			}
		} catch (Throwable $ex) {
			$this->logger->error($ex);
			$this->rollback();
			Throws::reThrow(DatabaseException::class, $ex);
		}

		return false;
	}

	/**
	 * @template TFieldArray of FieldArrayAlias
	 * @phpstan-return DatabaseTableResult<TFieldArray>
	 */
	public function query(string $statement, ?array $parameters = null): DatabaseTableResult
	{
		$query = $this->executeStatement($statement, $parameters);

		/** @phpstan-var DatabaseTableResult<TFieldArray> */
		$result = $this->convertTableResult($query);

		return $result;
	}

	/**
	 * @template TFieldArray of FieldArrayAlias
	 * @phpstan-return DatabaseRowResult<TFieldArray>
	 */
	public function queryFirst(string $statement, ?array $parameters = null): DatabaseRowResult
	{
		$query = $this->executeStatement($statement, $parameters);

		/** @phpstan-var DatabaseRowResult<TFieldArray> */
		$result = $this->convertRowResult($query);
		if (ArrayUtility::isNullOrEmpty($result->fields)) { //@phpstan-ignore-line 本クラス内限定で空の可能性あり
			throw new DatabaseException($this->getErrorMessage());
		}

		return $result;
	}

	/**
	 * @template TFieldArray of FieldArrayAlias
	 * @phpstan-return DatabaseRowResult<TFieldArray>
	 */
	public function queryFirstOrNull(string $statement, ?array $parameters = null): ?DatabaseRowResult
	{
		$query = $this->executeStatement($statement, $parameters);

		/** @phpstan-var DatabaseRowResult<TFieldArray> */
		$result = $this->convertRowResult($query);
		$result = $query->fetch();
		if (ArrayUtility::isNullOrEmpty($result->fields)) {
			return null;
		}

		return $result;
	}

	/**
	 * @template TFieldArray of FieldArrayAlias
	 * @phpstan-return DatabaseRowResult<TFieldArray>
	 */
	public function querySingle(string $statement, ?array $parameters = null): DatabaseRowResult
	{
		$query = $this->executeStatement($statement, $parameters);

		/** @phpstan-var DatabaseRowResult<TFieldArray> */
		$result = $this->convertRowResult($query);
		if (ArrayUtility::isNullOrEmpty($result->fields)) { //@phpstan-ignore-line 本クラス内限定で空の可能性あり
			throw new DatabaseException($this->getErrorMessage());
		}

		$next = $query->fetch();
		if ($next !== false) {
			throw new DatabaseException($this->getErrorMessage());
		}

		return $result;
	}

	/**
	 * @template TFieldArray of FieldArrayAlias
	 * @phpstan-return DatabaseRowResult<TFieldArray>
	 */
	public function querySingleOrNull(string $statement, ?array $parameters = null): ?DatabaseRowResult
	{
		$query = $this->executeStatement($statement, $parameters);

		/** @phpstan-var DatabaseRowResult<TFieldArray> */
		$result = $this->convertRowResult($query);
		if (ArrayUtility::isNullOrEmpty($result->fields)) { //@phpstan-ignore-line 本クラス内限定で空の可能性あり
			return null;
		}

		$next = $query->fetch();
		if ($next !== false) {
			throw new DatabaseException($this->getErrorMessage());
		}

		return $result;
	}

	/**
	 * ソートを強制。
	 *
	 * 単純な文字列処理のため無理な時は無理。
	 *
	 * @param string $statement
	 * @return void
	 */
	protected function enforceOrdered(string $statement): void
	{
		if (!Regex::isMatch($statement, '/\\border\\s+by\\b/i')) {
			throw new SqlException();
		}
	}

	/**
	 * @template TFieldArray of FieldArrayAlias
	 * @phpstan-return DatabaseTableResult<TFieldArray>
	 */
	public function selectOrdered(string $statement, ?array $parameters = null): DatabaseTableResult
	{
		$this->enforceOrdered($statement);

		/** @phpstan-var DatabaseTableResult<TFieldArray> */
		$result = $this->query($statement, $parameters);

		return $result;
	}

	/**
	 * 単独件数取得を強制。
	 *
	 * 単純な文字列処理のため無理な時は無理。
	 *
	 * @param string $statement
	 * @return void
	 */
	protected function enforceSingleCount(string $statement): void
	{
		if (!Regex::isMatch($statement, '/\\bselect\\s+count\\s*\\(/i')) {
			throw new SqlException();
		}
	}

	public function selectSingleCount(string $statement, ?array $parameters = null): int
	{
		$this->enforceSingleCount($statement);

		/** @-var array<string,mixed> */
		$result = $this->queryFirst($statement, $parameters);
		$val = strval(current($result->fields));
		if (TypeUtility::tryParseInteger($val, $count)) {
			return $count;
		}

		throw new DatabaseException();
	}

	/**
	 * @template TFieldArray of FieldArrayAlias
	 * @phpstan-return DatabaseTableResult<TFieldArray>
	 */
	public function execute(string $statement, ?array $parameters = null): DatabaseTableResult
	{
		$query = $this->executeStatement($statement, $parameters);

		/** @phpstan-var DatabaseTableResult<TFieldArray> */
		$result = $this->convertTableResult($query);

		return $result;
	}

	/**
	 * INSERT文を強制。
	 *
	 * 単純な文字列処理のため無理な時は無理。
	 *
	 * @param string $statement
	 * @return void
	 */
	protected function enforceInsert(string $statement): void
	{
		if (!Regex::isMatch($statement, '/\\binsert\\b/i')) {
			throw new SqlException();
		}
	}

	public function insert(string $statement, ?array $parameters = null): int
	{
		$this->enforceInsert($statement);
		return $this->execute($statement, $parameters)->resultCount;
	}

	public function insertSingle(string $statement, ?array $parameters = null): void
	{
		$this->enforceInsert($statement);
		$result = $this->execute($statement, $parameters);
		if ($result->resultCount !== 1) {
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
	protected function enforceUpdate(string $statement): void
	{
		if (!Regex::isMatch($statement, '/\\bupdate\\b/i')) {
			throw new SqlException();
		}
	}

	public function update(string $statement, ?array $parameters = null): int
	{
		$this->enforceUpdate($statement);
		return $this->execute($statement, $parameters)->resultCount;
	}

	public function updateByKey(string $statement, ?array $parameters = null): void
	{
		$this->enforceUpdate($statement);
		$result = $this->execute($statement, $parameters);
		if ($result->resultCount !== 1) {
			throw new SqlException();
		}
	}

	public function updateByKeyOrNothing(string $statement, ?array $parameters = null): bool
	{
		$this->enforceUpdate($statement);
		$result = $this->execute($statement, $parameters);
		if (1 < $result->resultCount) {
			throw new SqlException();
		}

		return $result->resultCount === 1;
	}

	/**
	 * DELETE文を強制。
	 *
	 * 単純な文字列処理のため無理な時は無理。
	 *
	 * @param string $statement
	 * @return void
	 */
	protected function enforceDelete(string $statement): void
	{
		if (!Regex::isMatch($statement, '/\\bdelete\\b/i')) {
			throw new SqlException();
		}
	}

	public function delete(string $statement, ?array $parameters = null): int
	{
		$this->enforceDelete($statement);
		return $this->execute($statement, $parameters)->resultCount;
	}

	public function deleteByKey(string $statement, ?array $parameters = null): void
	{
		$this->enforceDelete($statement);
		$result = $this->execute($statement, $parameters);
		if ($result->resultCount !== 1) {
			throw new SqlException();
		}
	}

	public function deleteByKeyOrNothing(string $statement, ?array $parameters = null): bool
	{
		$this->enforceDelete($statement);
		$result = $this->execute($statement, $parameters);
		if (1 < $result->resultCount) {
			throw new SqlException();
		}

		return $result->resultCount === 1;
	}
}
