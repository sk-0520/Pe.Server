<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use PeServer\Core\Throws\SqlException;
use PeServer\Core\Throws\DatabaseException;
use PeServer\Core\Throws\ObjectDisposedException;

interface IDatabaseReader
{
	/**
	 * 問い合わせを実行。
	 *
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @return array<array<string,mixed>>
	 * @throws \PDOException
	 * @throws DatabaseException
	 * @throws ObjectDisposedException
	 */
	public function query(string $statement, ?array $parameters = null): array;

	/**
	 * 問い合わせの最初のデータを取得。
	 *
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @return array<string,mixed>
	 * @throws \PDOException
	 * @throws DatabaseException
	 * @throws ObjectDisposedException
	 */
	public function queryFirst(string $statement, ?array $parameters = null): array;

	/**
	 * 問い合わせの最初のデータを取得。存在しない場合に $defaultValue を返す。
	 *
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string,mixed>|null $defaultValue 戻り。
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @return array<string,mixed>|null
	 * @throws \PDOException
	 * @throws DatabaseException
	 * @throws ObjectDisposedException
	 */
	public function queryFirstOr(?array $defaultValue, string $statement, ?array $parameters = null): ?array;

	/**
	 * 1件だけの問い合わせを実行。
	 *
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @return array<string,mixed>
	 * @throws \PDOException
	 * @throws DatabaseException
	 * @throws ObjectDisposedException
	 */
	public function querySingle(string $statement, ?array $parameters = null): array;

	/**
	 * 1件だけの問い合わせを実行。存在しない場合に $defaultValue を返す
	 *
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string,mixed>|null $defaultValue 戻り。
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @return array<string,mixed>|null
	 * @throws \PDOException
	 * @throws DatabaseException
	 * @throws ObjectDisposedException
	 */
	public function querySingleOr(?array $defaultValue, string $statement, ?array $parameters = null): ?array;

	/**
	 * 並び順問い合わせ文を強制。
	 *
	 * 単純な文字列処理のため無理な時は無理。
	 *
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @return array<array<string,mixed>>
	 * @throws \PDOException
	 * @throws DatabaseException
	 * @throws SqlException 問い合わせ文の検証エラー
	 * @throws ObjectDisposedException
	 */
	public function selectOrdered(string $statement, ?array $parameters = null): array;

	/**
	 * 単一 COUNT 関数問い合わせ文を強制。
	 *
	 * 単純な文字列処理のため無理な時は無理。
	 *
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @return integer
	 * @throws \PDOException
	 * @throws DatabaseException
	 * @throws SqlException 問い合わせ文の検証エラー
	 * @throws ObjectDisposedException
	 */
	public function selectSingleCount(string $statement, ?array $parameters = null): int;
}
