<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use Exception;
use PDO;
use PDOException;
use PDOStatement;
use Throwable;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Database\ConnectionSetting;
use PeServer\Core\Database\DatabaseColumn;
use PeServer\Core\Database\DatabaseSequenceResult;
use PeServer\Core\Database\DatabaseTableResult;
use PeServer\Core\Database\IDatabaseTransactionContext;
use PeServer\Core\DisposerBase;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Regex;
use PeServer\Core\Text;
use PeServer\Core\Throws\DatabaseException;
use PeServer\Core\Throws\DatabaseInvalidQueryException;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Throws\SqlException;
use PeServer\Core\Throws\Throws;
use PeServer\Core\Throws\TransactionException;
use PeServer\Core\TypeUtility;

/**
 * DB処理。
 */
class DatabaseContext extends DisposerBase implements IDatabaseTransactionContext
{
	#region variable

	/**
	 * 接続処理。
	 */
	protected readonly PDO $pdo;

	/**
	 * ロガー
	 */
	protected readonly ILogger $logger;

	private Regex $regex;

	#endregion

	/**
	 * 生成。
	 *
	 * @param ConnectionSetting $setting
	 * @param ILogger $logger
	 * @throws DatabaseException
	 */
	public function __construct(ConnectionSetting $setting, ILogger $logger)
	{
		$this->logger = $logger;
		$this->regex = new Regex();

		$this->pdo = Throws::wrap(PDOException::class, DatabaseException::class, fn () => new PDO($setting->dsn, $setting->user, $setting->password, $setting->options));
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //cspell:disable-line
		$this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); //cspell:disable-line
	}

	#region function

	/**
	 * 直近のエラーメッセージを取得。
	 *
	 * @return string
	 */
	private function getErrorMessage(): string
	{
		return Text::dump($this->pdo->errorInfo());
	}

	/**
	 * バインド実行。
	 *
	 * @param PDOStatement $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @phpstan-param array<array-key,globa-alias-database-bind-value>|null $parameters
	 * @return void
	 */
	private function setParameters(PDOStatement $statement, ?array $parameters): void
	{
		if ($parameters !== null) {
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
	 * @phpstan-param literal-string $statement
	 * @phpstan-param array<array-key,globa-alias-database-bind-value>|null $parameters
	 * @return PDOStatement
	 * @throws SqlException 実行失敗。
	 */
	private function executeStatement(string $statement, ?array $parameters): PDOStatement
	{
		$this->throwIfDisposed();

		/** @var PDOStatement|false|null */
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
	 * @template TFieldArray of globa-alias-database-field-array
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
	 * @template TFieldArray of globa-alias-database-field-array
	 * @param PDOStatement $pdoStatement
	 * @return DatabaseTableResult
	 * @phpstan-return DatabaseTableResult<TFieldArray>
	 */
	private function convertTableResult(PDOStatement $pdoStatement): DatabaseTableResult
	{
		$columns = $this->getColumns($pdoStatement);

		$resultCount = $pdoStatement->rowCount();

		$rows = $pdoStatement->fetchAll();
		// @phpstan-ignore-next-line: [PHP_VERSION]
		if ($rows === false) {
			throw new DatabaseException($this->getErrorMessage());
		}

		$result = new DatabaseTableResult($columns, $resultCount, $rows);

		return $result; //@phpstan-ignore-line 空フィールドはない
	}

	/**
	 * 逐次データセットに変換。
	 *
	 * @template TFieldArray of globa-alias-database-field-array
	 * @param PDOStatement $pdoStatement
	 * @return DatabaseSequenceResult
	 * @phpstan-return DatabaseSequenceResult<TFieldArray>
	 */
	private function convertSequenceResult(PDOStatement $pdoStatement): DatabaseSequenceResult
	{
		$columns = $this->getColumns($pdoStatement);

		/** @var DatabaseSequenceResult<TFieldArray> */
		$result = new DatabaseSequenceResult($columns, $pdoStatement);

		return $result;
	}

	#endregion

	#region DisposerBase

	protected function disposeImpl(): void
	{
		if ($this->inTransaction()) {
			$this->rollback();
		}

		parent::disposeImpl();
	}

	#endregion

	#region IDatabaseTransactionContext

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

	public function escapeLike(string $value): string
	{
		return Text::replace(
			$value,
			["\\", '%', '_'],
			["\\\\", '\\%', '\\_'],
		);
	}

	public function escapeValue(mixed $value): string
	{
		if ($value === null) {
			return 'null';
		}

		return $this->pdo->quote($value);
	}

	/**
	 * @template TFieldArray of globa-alias-database-field-array
	 * @phpstan-return DatabaseSequenceResult<TFieldArray>
	 */
	public function fetch(string $statement, ?array $parameters = null): DatabaseSequenceResult
	{
		$query = $this->executeStatement($statement, $parameters);

		/** @phpstan-var DatabaseSequenceResult<TFieldArray> */
		$result = $this->convertSequenceResult($query);

		return $result;
	}

	/**
	 * @template TFieldArray of globa-alias-database-field-array
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
	 * @template TFieldArray of globa-alias-database-field-array
	 * @phpstan-return DatabaseRowResult<TFieldArray>
	 */
	public function queryFirst(string $statement, ?array $parameters = null): DatabaseRowResult
	{
		$query = $this->executeStatement($statement, $parameters);

		/** @phpstan-var DatabaseRowResult<TFieldArray> */
		$result = $this->convertRowResult($query);
		if (Arr::isNullOrEmpty($result->fields)) {
			throw new DatabaseInvalidQueryException($this->getErrorMessage());
		}

		return $result;
	}

	/**
	 * @template TFieldArray of globa-alias-database-field-array
	 * @phpstan-return DatabaseRowResult<TFieldArray>
	 */
	public function queryFirstOrNull(string $statement, ?array $parameters = null): ?DatabaseRowResult
	{
		$query = $this->executeStatement($statement, $parameters);

		/** @phpstan-var DatabaseRowResult<TFieldArray> */
		$result = $this->convertRowResult($query);
		if (Arr::isNullOrEmpty($result->fields)) {
			return null;
		}

		return $result;
	}

	/**
	 * @template TFieldArray of globa-alias-database-field-array
	 * @phpstan-return DatabaseRowResult<TFieldArray>
	 */
	public function querySingle(string $statement, ?array $parameters = null): DatabaseRowResult
	{
		$query = $this->executeStatement($statement, $parameters);

		/** @phpstan-var DatabaseRowResult<TFieldArray> */
		$result = $this->convertRowResult($query);
		if (Arr::isNullOrEmpty($result->fields)) {
			throw new DatabaseInvalidQueryException($this->getErrorMessage());
		}

		$next = $query->fetch();
		if ($next !== false) {
			throw new DatabaseInvalidQueryException($this->getErrorMessage());
		}

		return $result;
	}

	/**
	 * @template TFieldArray of globa-alias-database-field-array
	 * @phpstan-return DatabaseRowResult<TFieldArray>
	 */
	public function querySingleOrNull(string $statement, ?array $parameters = null): ?DatabaseRowResult
	{
		$query = $this->executeStatement($statement, $parameters);

		/** @phpstan-var DatabaseRowResult<TFieldArray> */
		$result = $this->convertRowResult($query);
		if (Arr::isNullOrEmpty($result->fields)) {
			return null;
		}

		$next = $query->fetch();
		if ($next !== false) {
			return null;
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
	protected function throwIfInvalidOrdered(string $statement): void
	{
		if (!$this->regex->isMatch($statement, '/\\border\\s+by\\b/i')) {
			throw new SqlException('order by');
		}
	}

	/**
	 * @template TFieldArray of globa-alias-database-field-array
	 * @phpstan-return DatabaseTableResult<TFieldArray>
	 */
	public function selectOrdered(string $statement, ?array $parameters = null): DatabaseTableResult
	{
		$this->throwIfInvalidOrdered($statement);

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
	protected function throwIfInvalidSingleCount(string $statement): void
	{
		if (!$this->regex->isMatch($statement, '/\\bselect\\s+count\\s*\\(/i')) {
			throw new SqlException('select count');
		}
	}

	public function selectSingleCount(string $statement, ?array $parameters = null): int
	{
		$this->throwIfInvalidSingleCount($statement);

		$result = $this->queryFirst($statement, $parameters);
		$val = strval(current($result->fields));
		if (TypeUtility::tryParseUInteger($val, $count)) {
			return $count;
		}

		throw new DatabaseInvalidQueryException();
	}

	/**
	 * @template TFieldArray of globa-alias-database-field-array
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
	protected function throwIfInvalidInsert(string $statement): void
	{
		if (!$this->regex->isMatch($statement, '/\\binsert\\b/i')) {
			throw new SqlException('insert');
		}
	}

	public function insert(string $statement, ?array $parameters = null): int
	{
		$this->throwIfInvalidInsert($statement);
		return $this->execute($statement, $parameters)->getResultCount();
	}

	public function insertSingle(string $statement, ?array $parameters = null): void
	{
		$this->throwIfInvalidInsert($statement);
		$result = $this->execute($statement, $parameters);
		if ($result->getResultCount() !== 1) {
			throw new DatabaseInvalidQueryException();
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
	protected function throwIfInvalidUpdate(string $statement): void
	{
		if (!$this->regex->isMatch($statement, '/\\bupdate\\b/i')) {
			throw new SqlException('update');
		}
	}

	public function update(string $statement, ?array $parameters = null): int
	{
		$this->throwIfInvalidUpdate($statement);
		return $this->execute($statement, $parameters)->getResultCount();
	}

	public function updateByKey(string $statement, ?array $parameters = null): void
	{
		$this->throwIfInvalidUpdate($statement);
		$result = $this->execute($statement, $parameters);
		if ($result->getResultCount() !== 1) {
			throw new DatabaseInvalidQueryException();
		}
	}

	public function updateByKeyOrNothing(string $statement, ?array $parameters = null): bool
	{
		$this->throwIfInvalidUpdate($statement);
		$result = $this->execute($statement, $parameters);
		if (1 < $result->getResultCount()) {
			throw new DatabaseInvalidQueryException();
		}

		return $result->getResultCount() === 1;
	}

	/**
	 * DELETE文を強制。
	 *
	 * 単純な文字列処理のため無理な時は無理。
	 *
	 * @param string $statement
	 * @return void
	 */
	protected function throwIfInvalidDelete(string $statement): void
	{
		if (!$this->regex->isMatch($statement, '/\\bdelete\\b/i')) {
			throw new SqlException('delete');
		}
	}

	public function delete(string $statement, ?array $parameters = null): int
	{
		$this->throwIfInvalidDelete($statement);
		return $this->execute($statement, $parameters)->getResultCount();
	}

	public function deleteByKey(string $statement, ?array $parameters = null): void
	{
		$this->throwIfInvalidDelete($statement);
		$result = $this->execute($statement, $parameters);
		if ($result->getResultCount() !== 1) {
			throw new DatabaseInvalidQueryException();
		}
	}

	public function deleteByKeyOrNothing(string $statement, ?array $parameters = null): bool
	{
		$this->throwIfInvalidDelete($statement);
		$result = $this->execute($statement, $parameters);
		if (1 < $result->getResultCount()) {
			throw new DatabaseInvalidQueryException();
		}

		return $result->getResultCount() === 1;
	}

	#endregion
}
