<?php

declare(strict_types=1);

namespace PeServer\Core;

use \PDO;
use \PDOStatement;

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
				$statement->bindParam($key, $value);
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
}
