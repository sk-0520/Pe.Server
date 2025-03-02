<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use PeServer\Core\Collections\Arr;
use PeServer\Core\Database\DatabaseResultBase;
use PeServer\Core\Serialization\IMapper;
use PeServer\Core\Serialization\Mapper;

/**
 * 問い合わせ結果。
 *
 * @template TFieldArray of globa-alias-database-field-array
 * @immutable
 */
class DatabaseTableResult extends DatabaseResultBase
{
	/**
	 * 生成。
	 *
	 * @param array<array<string|int,mixed>> $rows レコード一覧。各レコードにフィールド配列が格納されている。
	 * @phpstan-param TFieldArray[] $rows
	 */
	public function __construct(
		array $columns,
		int $resultCount,
		public array $rows
	) {
		parent::__construct($columns, $resultCount);
	}

	#region function

	/**
	 * レコード件数。
	 *
	 * @return int
	 * @phpstan-return non-negative-int
	 */
	public function getRowsCount(): int
	{
		return Arr::getCount($this->rows);
	}

	/**
	 * 結果をマッピング。
	 *
	 * @template TObject of object
	 * @param string $className
	 * @phpstan-param class-string<TObject> $className
	 * @param IMapper|null $mapper
	 * @return array
	 * @phpstan-return TObject[]
	 */
	public function mapping(string $className, IMapper $mapper = null): array
	{
		/** @var TObject[] */
		$result = [];
		$instanceMapper = $mapper ?? new Mapper();

		foreach ($this->rows as $fields) {
			/** @phpstan-var TObject */
			$object = $this->mappingImpl($fields, $className, $instanceMapper);
			$result[] = $object;
		}

		return $result;
	}


	#endregion
}
