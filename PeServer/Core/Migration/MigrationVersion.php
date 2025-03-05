<?php

declare(strict_types=1);

namespace PeServer\Core\Migration;

use Attribute;
use PeServer\Core\Throws\ArgumentException;
use ReflectionClass;

/**
 * セットアップ処理で使用されるバージョン情報。
 */
#[Attribute(Attribute::TARGET_CLASS)]
readonly class MigrationVersion
{
	/**
	 * 生成。
	 *
	 * @param int $version セットアップバージョン。
	 */
	public function __construct(
		public int $version
	) {
		//NOP
	}

	#region function

	/**
	 * [汎用] バージョン取得
	 *
	 * @template T of object
	 * @param string|object $objectOrClassName
	 * @phpstan-param class-string<T>|T $objectOrClassName
	 * @return int
	 */
	public static function getVersion(string|object $objectOrClassName): int
	{
		$rc = new ReflectionClass($objectOrClassName);
		$attrs = $rc->getAttributes(static::class);
		if(empty($attrs)) {
			throw new ArgumentException("not found " . self::class);
		}
		$attr = $attrs[0];

		/** @var MigrationVersion */
		$obj = $attr->newInstance();

		return $obj->version;
	}

	#endregion
}
