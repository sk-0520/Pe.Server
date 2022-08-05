<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use PeServer\Core\Throws\DatabaseException;
use PeServer\Core\Throws\ObjectDisposedException;
use PeServer\Core\Throws\SqlException;

interface IDatabaseReader
{
	/**
	 * 問い合わせを実行。
	 *
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @phpstan-param array<array-key,DatabaseBindValueAlias>|null $parameters
	 * @return DatabaseTableResult
	 * @throws DatabaseException
	 * @throws SqlException 問い合わせ文の検証エラー
	 */
	public function query(string $statement, ?array $parameters = null): DatabaseTableResult;

	/**
	 * 問い合わせの最初のデータを取得。
	 *
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @phpstan-param array<array-key,DatabaseBindValueAlias>|null $parameters
	 * @return DatabaseRowResult
	 * @throws DatabaseException
	 * @throws SqlException 問い合わせ文の検証エラー
	 */
	public function queryFirst(string $statement, ?array $parameters = null): DatabaseRowResult;

	/**
	 * 問い合わせの最初のデータを取得。存在しない場合に `null` を返す。
	 *
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @phpstan-param array<array-key,DatabaseBindValueAlias>|null $parameters
	 * @return DatabaseRowResult|null
	 * @throws DatabaseException
	 * @throws SqlException 問い合わせ文の検証エラー
	 */
	public function queryFirstOrNull(string $statement, ?array $parameters = null): ?DatabaseRowResult;

	/**
	 * 1件だけの問い合わせを実行。
	 *
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @phpstan-param array<array-key,DatabaseBindValueAlias>|null $parameters
	 * @return DatabaseRowResult
	 * @throws DatabaseException
	 * @throws SqlException 問い合わせ文の検証エラー
	 */
	public function querySingle(string $statement, ?array $parameters = null): DatabaseRowResult;

	/**
	 * 1件だけの問い合わせを実行。存在しない場合に `null` を返す
	 *
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @phpstan-param array<array-key,DatabaseBindValueAlias>|null $parameters
	 * @return DatabaseRowResult|null
	 * @throws DatabaseException
	 * @throws SqlException 問い合わせ文の検証エラー
	 */
	public function querySingleOrNull(string $statement, ?array $parameters = null): ?DatabaseRowResult;

	/**
	 * 並び順問い合わせ文を強制。
	 *
	 * 単純な文字列処理のため無理な時は無理。
	 *
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @phpstan-param array<array-key,DatabaseBindValueAlias>|null $parameters
	 * @return DatabaseTableResult
	 * @throws DatabaseException
	 * @throws SqlException 問い合わせ文の検証エラー
	 */
	public function selectOrdered(string $statement, ?array $parameters = null): DatabaseTableResult;

	/**
	 * 単一 COUNT 関数問い合わせ文を強制。
	 *
	 * 単純な文字列処理のため無理な時は無理。
	 *
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @phpstan-param array<array-key,DatabaseBindValueAlias>|null $parameters
	 * @return integer
	 * @throws DatabaseException
	 * @throws SqlException 問い合わせ文の検証エラー
	 */
	public function selectSingleCount(string $statement, ?array $parameters = null): int;
}
