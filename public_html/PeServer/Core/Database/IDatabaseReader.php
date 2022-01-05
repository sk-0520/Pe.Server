<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

interface IDatabaseReader
{
	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @return array<array<string,mixed>>
	 */
	public function query(string $statement, ?array $parameters = null): array;

	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @return array<string,mixed>
	 */
	public function queryFirst(string $statement, ?array $parameters = null): array;

	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string,mixed>|null $defaultValue 戻り。
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @return array<string,mixed>|null
	 */
	public function queryFirstOr(?array $defaultValue, string $statement, ?array $parameters = null): ?array;

	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @return array<string,mixed>
	 */
	public function querySingle(string $statement, ?array $parameters = null): array;

	/**
	 * Undocumented function
	 *
	 * @param string $statement
	 * @param array<string,mixed>|null $defaultValue 戻り。
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @return array<string,mixed>|null
	 */
	public function querySingleOr(?array $defaultValue, string $statement, ?array $parameters = null): ?array;

	/**
	 * 並ぶ順問い合わせ文を強制。
	 *
	 * 単純な文字列処理のため無理な時は無理。
	 *
	 * @param string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @return mixed[]
	 */
	public function selectOrdered(string $statement, ?array $parameters = null): array;

	/**
	 * 単一 COUNT 関数問い合わせ文を強制。
	 *
	 * 単純な文字列処理のため無理な時は無理。
	 *
	 * @param string $statement
	 * @param array<string|int,string|int|bool>|null $parameters
	 * @return integer
	 */
	public function selectSingleCount(string $statement, ?array $parameters = null): int;
}
