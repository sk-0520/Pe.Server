<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use PeServer\Core\Serialization\IMapper;
use PeServer\Core\Serialization\Mapper;

/**
 * 問い合わせ結果格納データ基底。
 */
abstract class DatabaseResultBase
{
	/**
	 * 生成。
	 *
	 * @param DatabaseColumn[] $columns カラム情報(取得成功したものだけ格納されている)。
	 * @param int $resultCount 実行影響件数。
	 * @phpstan-param UnsignedIntegerAlias $resultCount
	 */
	public function __construct(
		/** @readonly */
		public array $columns,
		/** @readonly */
		private int $resultCount
	) {
	}

	#region function

	/**
	 * 実行影響件数を取得。
	 *
	 * @return int
	 * @phpstan-return UnsignedIntegerAlias
	 */
	public function getResultCount(): int
	{
		return $this->resultCount;
	}

	/**
	 * 行データに対してオブジェクトマッピング処理。
	 *
	 * 上位でとりあえずいい感じにしとく感じで。
	 *
	 * @template TFieldArray of FieldArrayAlias
	 * @template TObject of object
	 * @param array $fields
	 * @phpstan-param TFieldArray $fields
	 * @param string|object $classNameOrObject
	 * @phpstan-param class-string<TObject>|TObject $classNameOrObject
	 * @param IMapper|null $mapper マッピング処理。未指定なら `Mapper` を使用する。
	 * @return object
	 * @phpstan-return TObject
	 */
	protected function convertRow(array $fields, string|object $classNameOrObject, IMapper $mapper = null): object
	{
		$object = is_string($classNameOrObject)
			? new $classNameOrObject()
			: $classNameOrObject;

		$mapper ??= new Mapper();
		$mapper->mapping($fields, $object);

		return $object;
	}

	//public abstract function mapping(string|object $classNameOrObject, IMapper $mapper = null): mixed;

	#endregion
}
