<?php

declare(strict_types=1);

namespace PeServer\Core\Serialization;

use Attribute;

/**
 * マッピング設定。
 *
 * * `FLAG_IGNORE` が設定されていない限り処理される。
 *
 * @immutable
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Mapping
{
	/** 無視する。 */
	public const FLAG_IGNORE = 0b11111111;
	/** 通常。 */
	public const FLAG_NONE = 0b00000000;
	/** キーがない場合に例外を投げる(指定しない場合は無視される)。 */
	public const FLAG_EXCEPTION_NOT_FOUND_KEY = 0b00000001;
	/** 設定値の型が合わない場合に例外を投げる(指定しない場合は型変換を行い、それでも無理なら無視されるが `settype` がクッソ頑張ってる)。 */
	public const FLAG_EXCEPTION_TYPE_MISMATCH = 0b00000010;
	/** オブジェクトの場合に生成しない(指定しない場合は `null` だったら生成する)。 */
	public const FLAG_OBJECT_INSTANCE_ONLY = 0b00000100;

	/**
	 * 生成。
	 *
	 * @param string $name 対象キー名。未設定の場合はプロパティ名から判定する。
	 * @param int $flags 各種設定。
	 * @phpstan-param int-mask-of<self::FLAG_*> $flags 各種設定。
	 */
	public function __construct(
		public string $name = '',
		public int $flags = self::FLAG_NONE
	) {
	}
}
