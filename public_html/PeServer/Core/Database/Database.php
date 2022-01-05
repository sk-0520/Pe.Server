<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use \PDO;
use \PDOStatement;
use PeServer\Core\ILogger;
use PeServer\Core\Log\Logging;
use PeServer\Core\ArrayUtility;
use PeServer\Core\StringUtility;
use PeServer\Core\Throws\Throws;
use PeServer\Core\Throws\SqlException;

/**
 * DB接続処理。
 */
class Database implements IDatabaseContext
{
	/**
	 * 接続処理。
	 */
	private PDO $pdo;

	private ILogger $logger;


	/**
	 * Undocumented function
	 *
	 * @param string $dsn
	 * @param string $user
	 * @param string $password
	 * @param array<mixed>|null $option
	 * @param ILogger $logger
	 */
	public function __construct(string $dsn, string $user, string $password, ?array $option, ILogger $logger)
	{
		$this->logger = $logger;

		$this->pdo = new PDO($dsn, $user, $password, $option);
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
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
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @return PDOStatement
	 * @throws SqlException 実行失敗。
	 */
	private function executeStatement(string $statement, ?array $parameters): PDOStatement
	{
		$query = $this->pdo->prepare($statement);

		$this->setParameters($query, $parameters);

		if (!$query->execute()) {
			throw new SqlException($this->getErrorMessage());
		}

		return $query;
	}

	/**
	 * トランザクション開始。
	 *
	 * @return void
	 * @throws \PDOException
	 * @throws SqlException
	 */
	public function beginTransaction(): void
	{
		if (!$this->pdo->beginTransaction()) {
			throw new SqlException($this->getErrorMessage()); // これが投げられず PDOException が投げられると思う
		}
	}

	/**
	 * トランザクションの確定。
	 *
	 * @return void
	 * @throws \PDOException
	 * @throws SqlException
	 */
	public function commit(): void
	{
		if (!$this->pdo->commit()) {
			throw new SqlException($this->getErrorMessage());
		}
	}

	/**
	 * トランザクションの取消。
	 *
	 * @return void
	 * @throws \PDOException
	 * @throws SqlException
	 */
	public function rollback(): void
	{
		if (!$this->pdo->rollback()) {
			throw new SqlException($this->getErrorMessage());
		}
	}

	/**
	 * トランザクションラップ処理。
	 *
	 * @param callable(IDatabaseContext $context,mixed ...$arguments): bool $callback 実際の処理。戻り値が真の場合にコミット、偽ならロールバック。
	 * @param mixed ...$arguments 引数
	 * @return bool コミットされたか。正常系としてのコミット・ロールバック処理の戻りであり、異常系は例外が投げられる。
	 * @throws SqlException
	 */
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
			Throws::reThrow(SqlException::class, $ex);
		}

		return false;
	}

	public function query(string $statement, ?array $parameters = null): array
	{
		$query = $this->executeStatement($statement, $parameters);

		$result = $query->fetchAll();
		if ($result === false) {
			throw new SqlException($this->getErrorMessage());
		}

		return $result;
	}

	public function queryFirst(string $statement, ?array $parameters = null): array
	{
		$query = $this->executeStatement($statement, $parameters);

		$result = $query->fetch();
		if ($result === false) {
			throw new SqlException($this->getErrorMessage());
		}

		return $result;
	}

	public function queryFirstOr(?array $defaultValue, string $statement, ?array $parameters = null): ?array
	{
		$query = $this->executeStatement($statement, $parameters);

		$result = $query->fetch();
		if ($result === false) {
			return $defaultValue;
		}

		return $result;
	}

	public function querySingle(string $statement, ?array $parameters = null): array
	{
		$query = $this->executeStatement($statement, $parameters);

		$result = $query->fetch();
		if ($result === false) {
			throw new SqlException($this->getErrorMessage());
		}

		$next = $query->fetch();
		if ($next !== false) {
			throw new SqlException($this->getErrorMessage());
		}

		return $result;
	}

	public function querySingleOr(?array $defaultValue, string $statement, ?array $parameters = null): ?array
	{
		$query = $this->executeStatement($statement, $parameters);

		$result = $query->fetch();
		if ($result === false) {
			return $defaultValue;
		}

		$next = $query->fetch();
		if ($next !== false) {
			throw new SqlException($this->getErrorMessage());
		}

		return $result;
	}

	public function selectOrdered(string $statement, ?array $parameters = null): array
	{
		if (!preg_match('/\\border\\s+by\\b/i', $statement)) {
			throw new SqlException();
		}

		return $this->query($statement, $parameters);
	}

	public function selectSingleCount(string $statement, ?array $parameters = null): int
	{
		if (!preg_match('/\\bselect\\s+count\\s*\\(/i', $statement)) {
			throw new SqlException();
		}

		$result = $this->queryFirst($statement, $parameters);
		return (int)current($result);
	}

	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @return integer
	 */
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
		if (!preg_match('/\\binsert\\b/i', $statement)) {
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
		if (!preg_match('/\\bdelete\\b/i', $statement)) {
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
