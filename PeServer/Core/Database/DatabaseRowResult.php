<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use PeServer\Core\Database\DatabaseResultBase;
use PeServer\Core\Serialization\IMapper;
use PeServer\Core\Serialization\Mapper;

/**
 * 単一問い合わせ結果。
 *
 * @template TFieldArray of globa-alias-database-field-array
 * @immutable
 */
class DatabaseRowResult extends DatabaseResultBase
{
	/**
	 * 生成。
	 *
	 * @param array<string|int,mixed> $fields フィールド一覧。
	 * @phpstan-param TFieldArray $fields
	 */
	public function __construct(
		array $columns,
		int $resultCount,
		public readonly array $fields
	) {
		parent::__construct($columns, $resultCount);
	}

	#region function

	/**
	 * 結果をマッピング。
	 *
	 * @template TObject of object
	 * @param string|object $classNameOrObject
	 * @phpstan-param class-string<TObject>|TObject $classNameOrObject
	 * @param IMapper|null $mapper
	 * @return object
	 * @phpstan-return TObject
	 */
	public function mapping(string|object $classNameOrObject, IMapper $mapper = null): object
	{
		return $this->mappingImpl($this->fields, $classNameOrObject, $mapper ?? new Mapper());
	}

	#endregion
}
