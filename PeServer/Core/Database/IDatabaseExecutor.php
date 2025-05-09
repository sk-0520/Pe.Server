<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use PeServer\Core\Database\IDatabaseImplementation;
use PeServer\Core\Throws\DatabaseException;
use PeServer\Core\Throws\ObjectDisposedException;
use PeServer\Core\Throws\SqlException;

/**
 * `insert/update/delete` 系処理。
 *
 * DB固有の何かも行う(`PRAGMA/GRANT/USE` とか)
 */
interface IDatabaseExecutor extends IDatabaseImplementation
{
	#region function

	/**
	 * 実行処理。
	 *
	 * @template TFieldArray of globa-alias-database-field-array
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @phpstan-param array<array-key,globa-alias-database-bind-value>|null $parameters
	 * @return DatabaseTableResult
	 * @phpstan-return DatabaseTableResult<TFieldArray>
	 * @throws DatabaseException
	 */
	public function execute(string $statement, ?array $parameters = null): DatabaseTableResult;

	/**
	 * 挿入処理。
	 *
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @phpstan-param array<array-key,globa-alias-database-bind-value>|null $parameters
	 * @return int 挿入件数。
	 * @throws DatabaseException
	 * @throws SqlException 問い合わせ文の検証エラー
	 */
	public function insert(string $statement, ?array $parameters = null): int;

	/**
	 * 単一挿入処理。
	 *
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @phpstan-param array<array-key,globa-alias-database-bind-value>|null $parameters
	 * @return void
	 * @throws DatabaseException
	 * @throws SqlException 問い合わせ文の検証エラー
	 */
	public function insertSingle(string $statement, ?array $parameters = null): void;

	/**
	 * 更新処理。
	 *
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @phpstan-param array<array-key,globa-alias-database-bind-value>|null $parameters
	 * @return int 更新件数。
	 * @throws DatabaseException
	 * @throws SqlException 問い合わせ文の検証エラー
	 */
	public function update(string $statement, ?array $parameters = null): int;

	/**
	 * 単一更新処理。
	 *
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @phpstan-param array<array-key,globa-alias-database-bind-value>|null $parameters
	 * @return void
	 * @throws DatabaseException
	 * @throws SqlException 問い合わせ文の検証エラー
	 */
	public function updateByKey(string $statement, ?array $parameters = null): void;

	/**
	 * 単一更新処理。
	 *
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @phpstan-param array<array-key,globa-alias-database-bind-value>|null $parameters
	 * @return boolean 更新できたか。
	 * @throws DatabaseException
	 * @throws SqlException 問い合わせ文の検証エラー
	 */
	public function updateByKeyOrNothing(string $statement, ?array $parameters = null): bool;

	/**
	 * 削除処理。
	 *
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @phpstan-param array<array-key,globa-alias-database-bind-value>|null $parameters
	 * @return int 削除件数。
	 * @throws DatabaseException
	 * @throws SqlException 問い合わせ文の検証エラー
	 */
	public function delete(string $statement, ?array $parameters = null): int;

	/**
	 * 単一削除処理。
	 *
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @phpstan-param array<array-key,globa-alias-database-bind-value>|null $parameters
	 * @return void
	 * @throws DatabaseException
	 * @throws SqlException 問い合わせ文の検証エラー
	 */
	public function deleteByKey(string $statement, ?array $parameters = null): void;

	/**
	 * 単一削除処理。
	 *
	 * @param string $statement
	 * @phpstan-param literal-string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @phpstan-param array<array-key,globa-alias-database-bind-value>|null $parameters
	 * @return boolean 削除できたか。
	 * @throws DatabaseException
	 * @throws SqlException 問い合わせ文の検証エラー
	 */
	public function deleteByKeyOrNothing(string $statement, ?array $parameters = null): bool;

	#endregion
}
