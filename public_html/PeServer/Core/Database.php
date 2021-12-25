<?php

declare(strict_types=1);

namespace PeServer\Core;

use \PDO;
use \PDOStatement;
use PeServer\Core\Throws\NotImplementedException;
use \PeServer\Core\Throws\SqlException;

abstract class Database
{
	/**
	 * 初期化チェック
	 *
	 * @var InitializeChecker|null
	 */
	protected static $_initializeChecker;

	/**
	 * DB接続設定
	 *
	 * @var array<string,mixed>
	 */
	private static $_databaseConfiguration;

	/**
	 * Undocumented function
	 *
	 * @param array<string,mixed> $databaseConfiguration
	 * @return void
	 */
	public static function initialize(array $databaseConfiguration): void
	{
		if (is_null(self::$_initializeChecker)) {
			self::$_initializeChecker = new InitializeChecker();
		}
		self::$_initializeChecker->initialize();

		self::$_databaseConfiguration = $databaseConfiguration;
	}

	public static function open(): Database
	{
		self::$_initializeChecker->throwIfNotInitialize();

		return new _Database_Invisible(self::$_databaseConfiguration);
	}

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
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int> $parameters
	 * @return mixed[]
	 */
	public abstract function selectOrdered(string $statement, array $parameters = array()): array;
	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int> $parameters
	 * @return integer
	 */
	public abstract function selectSingleCount(string $statement, array $parameters = array()): int;

	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int> $parameters
	 * @return integer
	 */
	public abstract function insert(string $statement, array $parameters = array()): int;
	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int> $parameters
	 * @return void
	 */
	public abstract function insertSingle(string $statement, array $parameters = array()): void;

	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int> $parameters
	 * @return integer
	 */
	public abstract function update(string $statement, array $parameters = array()): int;
	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int> $parameters
	 * @return void
	 */
	public abstract function updateByKey(string $statement, array $parameters = array()): void;
	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int> $parameters
	 * @return boolean
	 */
	public abstract function updateByKeyOrNothing(string $statement, array $parameters = array()): bool;

	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int> $parameters
	 * @return integer
	 */
	public abstract function delete(string $statement, array $parameters = array()): int;
	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int> $parameters
	 * @return void
	 */
	public abstract function deleteByKey(string $statement, array $parameters = array()): void;
	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int> $parameters
	 * @return boolean
	 */
	public abstract function deleteByKeyOrNothing(string $statement, array $parameters = array()): bool;
}

class _Database_Invisible extends Database
{
	/**
	 * 接続処理。
	 *
	 * @var PDO
	 */
	private $pdo;

	/**
	 * 生成。
	 *
	 * @param array<string,string|mixed> $databaseConfiguration
	 */
	public function __construct(array $databaseConfiguration)
	{
		self::$_initializeChecker->throwIfNotInitialize();

		$dsn = 'sqlite:' . $databaseConfiguration['connection'];
		$this->pdo = new PDO($dsn);
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	}

	/**
	 * Undocumented function
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

	private function getErrorMessage(): string
	{
		return StringUtility::dump($this->pdo->errorInfo());
	}

	public function query(string $statement, array $parameters = array()): array
	{
		self::$_initializeChecker->throwIfNotInitialize();

		$query = $this->pdo->prepare($statement);

		$this->setParameters($query, $parameters);

		if (!$query->execute()) {
			throw new SqlException($this->getErrorMessage());
		}

		$result = $query->fetchAll();
		if ($result === false) {
			throw new SqlException($this->getErrorMessage());
		}

		return $result;
	}

	public function queryFirst(string $statement, array $parameters = array()): array
	{
		self::$_initializeChecker->throwIfNotInitialize();

		$query = $this->pdo->prepare($statement);

		$this->setParameters($query, $parameters);

		if (!$query->execute()) {
			throw new SqlException($this->getErrorMessage());
		}

		$result = $query->fetch();
		if ($result === false) {
			throw new SqlException($this->getErrorMessage());
		}

		return $result;
	}

	public function queryFirstOrDefault($defaultValue, string $statement, array $parameters = array())
	{
		self::$_initializeChecker->throwIfNotInitialize();

		$query = $this->pdo->prepare($statement);

		$this->setParameters($query, $parameters);

		if (!$query->execute()) {
			throw new SqlException($this->getErrorMessage());
		}

		$result = $query->fetch();
		if ($result === false) {
			return $defaultValue;
		}

		return $result;
	}

	public function execute(string $statement, array $parameters = array()): int
	{
		self::$_initializeChecker->throwIfNotInitialize();

		$query = $this->pdo->prepare($statement);

		$this->setParameters($query, $parameters);

		if (!$query->execute()) {
			throw new SqlException($this->getErrorMessage());
		}

		return $query->rowCount();
	}

	public function selectOrdered(string $statement, array $parameters = array()): array
	{
		if (!preg_match('/\\border\\s+by\\b/i', $statement)) {
			throw new SqlException();
		}

		return $this->query($statement, $parameters);
	}

	public function selectSingleCount(string $statement, array $parameters = array()): int
	{
		if (!preg_match('/\\bselect\\s+count\\s*\\(/i', $statement)) {
			throw new SqlException();
		}

		$result = $this->queryFirst($statement, $parameters);
		return (int)current($result);
	}

	private function enforceInsert(string $statement): void
	{
		if (!preg_match('/\\binsert\\b/i', $statement)) {
			throw new SqlException();
		}
	}

	public function insert(string $statement, array $parameters = array()): int
	{
		$this->enforceInsert($statement);
		return $this->execute($statement, $parameters);
	}
	public function insertSingle(string $statement, array $parameters = array()): void
	{
		$this->enforceInsert($statement);
		$result = $this->execute($statement, $parameters);
		if ($result !== 1) {
			throw new SqlException();
		}
	}

	private function enforceUpdate(string $statement): void
	{
		if (!preg_match('/\\bupdate\\b/i', $statement)) {
			throw new SqlException();
		}
	}

	public function update(string $statement, array $parameters = array()): int
	{
		$this->enforceUpdate($statement);
		return $this->execute($statement, $parameters);
	}
	public function updateByKey(string $statement, array $parameters = array()): void
	{
		$this->enforceUpdate($statement);
		$result = $this->execute($statement, $parameters);
		if ($result !== 1) {
			throw new SqlException();
		}
	}
	public function updateByKeyOrNothing(string $statement, array $parameters = array()): bool
	{
		$this->enforceUpdate($statement);
		$result = $this->execute($statement, $parameters);
		if (1 < $result) {
			throw new SqlException();
		}

		return $result === 1;
	}

	private function enforceDelete(string $statement): void
	{
		if (!preg_match('/\\bdelete\\b/i', $statement)) {
			throw new SqlException();
		}
	}

	public function delete(string $statement, array $parameters = array()): int
	{
		$this->enforceDelete($statement);
		return $this->execute($statement, $parameters);
	}
	public function deleteByKey(string $statement, array $parameters = array()): void
	{
		$this->enforceDelete($statement);
		$result = $this->execute($statement, $parameters);
		if ($result !== 1) {
			throw new SqlException();
		}
	}
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
