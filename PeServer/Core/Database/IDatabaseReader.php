<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use PeServer\Core\Database\DatabaseRowResult;
use PeServer\Core\Database\IDatabaseImplementation;
use PeServer\Core\Throws\DatabaseException;
use PeServer\Core\Throws\ObjectDisposedException;
use PeServer\Core\Throws\SqlException;

/**
 * `select` 系処理。
 *
 * 内容によっては実行処理がなされる。
 */
interface IDatabaseReader extends IDatabaseImplementation
{
	#region function

	/**
	 * 問い合わせを逐次実行。
	 *
	 * @template TFieldArray of globa-alias-field-array
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @phpstan-param array<array-key,globa-alias-database-bind-value>|null $parameters
	 * @return DatabaseSequenceResult
	 * @phpstan-return DatabaseSequenceResult<TFieldArray>
	 * @throws SqlException 問い合わせ文の検証エラー
	 */
	public function fetch(string $statement, ?array $parameters = null): DatabaseSequenceResult;

	/**
	 * 問い合わせを実行。
	 *
	 * @template TFieldArray of globa-alias-field-array
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @phpstan-param array<array-key,globa-alias-database-bind-value>|null $parameters
	 * @return DatabaseTableResult
	 * @phpstan-return DatabaseTableResult<TFieldArray>
	 * @throws DatabaseException
	 * @throws SqlException 問い合わせ文の検証エラー
	 */
	public function query(string $statement, ?array $parameters = null): DatabaseTableResult;

	/**
	 * 問い合わせの最初のデータを取得。
	 *
	 * @template TFieldArray of globa-alias-field-array
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @phpstan-param array<array-key,globa-alias-database-bind-value>|null $parameters
	 * @return DatabaseRowResult
	 * @phpstan-return DatabaseRowResult<TFieldArray>
	 * @throws DatabaseException
	 * @throws SqlException 問い合わせ文の検証エラー
	 */
	public function queryFirst(string $statement, ?array $parameters = null): DatabaseRowResult;

	/**
	 * 問い合わせの最初のデータを取得。存在しない場合に `null` を返す。
	 *
	 * @template TFieldArray of globa-alias-field-array
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @phpstan-param array<array-key,globa-alias-database-bind-value>|null $parameters
	 * @return DatabaseRowResult|null
	 * @phpstan-return DatabaseRowResult<TFieldArray>|null
	 * @throws DatabaseException
	 * @throws SqlException 問い合わせ文の検証エラー
	 */
	public function queryFirstOrNull(string $statement, ?array $parameters = null): ?DatabaseRowResult;

	/**
	 * 1件だけの問い合わせを実行。
	 *
	 * @template TFieldArray of globa-alias-field-array
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @phpstan-param array<array-key,globa-alias-database-bind-value>|null $parameters
	 * @return DatabaseRowResult
	 * @phpstan-return DatabaseRowResult<TFieldArray>
	 * @throws DatabaseException
	 * @throws SqlException 問い合わせ文の検証エラー
	 */
	public function querySingle(string $statement, ?array $parameters = null): DatabaseRowResult;

	/**
	 * 1件だけの問い合わせを実行。存在しない場合に `null` を返す
	 *
	 * @template TFieldArray of globa-alias-field-array
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @phpstan-param array<array-key,globa-alias-database-bind-value>|null $parameters
	 * @return DatabaseRowResult|null
	 * @phpstan-return DatabaseRowResult<TFieldArray>|null
	 * @throws DatabaseException
	 * @throws SqlException 問い合わせ文の検証エラー
	 */
	public function querySingleOrNull(string $statement, ?array $parameters = null): ?DatabaseRowResult;

	/**
	 * 並び順問い合わせ文を強制。
	 *
	 * 単純な文字列処理のため無理な時は無理。
	 *
	 * @template TFieldArray of globa-alias-field-array
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @phpstan-param array<array-key,globa-alias-database-bind-value>|null $parameters
	 * @return DatabaseTableResult
	 * @phpstan-return DatabaseTableResult<TFieldArray>
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
	 * @phpstan-param array<array-key,globa-alias-database-bind-value>|null $parameters
	 * @return int
	 * @phpstan-return non-negative-int
	 * @throws DatabaseException
	 * @throws SqlException 問い合わせ文の検証エラー
	 */
	public function selectSingleCount(string $statement, ?array $parameters = null): int;

	#endregion
}
