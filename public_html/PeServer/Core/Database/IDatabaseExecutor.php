<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

interface IDatabaseExecutor
{
	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @return integer
	 */
	public function execute(string $statement, ?array $parameters = null): int;

	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @return integer
	 */
	public function insert(string $statement, ?array $parameters = null): int;

	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @return void
	 */
	public function insertSingle(string $statement, ?array $parameters = null): void;

	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @return integer
	 */
	public function update(string $statement, ?array $parameters = null): int;

	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @return void
	 */
	public function updateByKey(string $statement, ?array $parameters = null): void;

	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @return boolean
	 */
	public function updateByKeyOrNothing(string $statement, ?array $parameters = null): bool;

	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @return integer
	 */
	public function delete(string $statement, ?array $parameters = null): int;

	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @return void
	 */
	public function deleteByKey(string $statement, ?array $parameters = null): void;

	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @return boolean
	 */
	public function deleteByKeyOrNothing(string $statement, ?array $parameters = null): bool;
}
