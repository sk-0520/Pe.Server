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
	 * @var array
	 */
	private static $_databaseConfiguration; // @phpstan-ignore-line

	public static function initialize(array $databaseConfiguration): void // @phpstan-ignore-line
	{
		if (is_null(self::$_initializeChecker)) {
			self::$_initializeChecker = new InitializeChecker();
		}
		self::$_initializeChecker->initialize();

		self::$_databaseConfiguration = $databaseConfiguration;
	}

	public static function create(): Database
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
	 * @return mixed
	 */
	public abstract function queryFirst(string $statement, array $parameters = array());
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
	 * Undocumented function
	 *
	 * @param array<string,string|mixed> $databaseConfiguration
	 */
	public function __construct(array $databaseConfiguration)
	{
		self::$_initializeChecker->throwIfNotInitialize();

		$this->pdo = new PDO('sqlite:' . $databaseConfiguration['connection']);
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
		foreach ($parameters as $key => $value) {
			$statement->bindParam($key, $value);
		}
	}

	public function query(string $statement, array $parameters = array()): array
	{
		self::$_initializeChecker->throwIfNotInitialize();

		$query = $this->pdo->prepare($statement);

		$this->setParameters($query, $parameters);

		$result = $query->fetchAll();
		if ($result === false) {
			throw new SqlException();
		}

		return $result;
	}

	public function queryFirst(string $statement, array $parameters = array())
	{
		self::$_initializeChecker->throwIfNotInitialize();

		$query = $this->pdo->prepare($statement);

		$this->setParameters($query, $parameters);

		$result = $query->fetch();
		if ($result === false) {
			throw new SqlException();
		}

		return $result;
	}
}
