<?php

use \PDOException;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Throws\SqlException;
use PeServer\Core\Throws\TransactionException;

interface IDatabaseTransactionContext extends IDatabaseContext
{
	/**
	 * トランザクション中か。
	 *
	 * @return bool
	 */
	public function isTransactions(): bool;

	/**
	 * トランザクション開始。
	 *
	 * @return void
	 * @throws PDOException
	 * @throws SqlException
	 * @throws TransactionException
	 */
	public function beginTransaction(): void;

	/**
	 * トランザクションの確定。
	 *
	 * @return void
	 * @throws PDOException
	 * @throws SqlException
	 * @throws TransactionException
	 */
	public function commit(): void;

	/**
	 * トランザクションの取消。
	 *
	 * @return void
	 * @throws PDOException
	 * @throws SqlException
	 * @throws TransactionException
	 */
	public function rollback(): void;

	/**
	 * トランザクションラップ処理。
	 *
	 * @param callable $callback 実際の処理。戻り値が真の場合にコミット、偽ならロールバック。
	 * @phpstan-param callable(IDatabaseContext $context,mixed ...$arguments): (bool) $callback
	 * @param mixed ...$arguments 引数
	 * @return bool コミットされたか。正常系としてのコミット・ロールバック処理の戻りであり、異常系は例外が投げられる。
	 * @throws SqlException
	 * @throws TransactionException
	 */
	public function transaction(callable $callback, ...$arguments): bool;
}
