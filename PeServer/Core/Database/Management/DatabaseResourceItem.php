<?php

declare(strict_types=1);

namespace PeServer\Core\Database\Management;

use PeServer\Core\Collections\Arr;
use PeServer\Core\Database\IDatabaseContext;

readonly class DatabaseResourceItem
{
	#region define

	public const KIND_TABLE = 0x01;
	public const KIND_VIEW = 0x02;
	public const KIND_MATERIALIZEDVIEW = 0x04;
	public const KIND_INDEX = 0x08;
	public const KIND_ALL = self::KIND_TABLE | self::KIND_VIEW | self::KIND_MATERIALIZEDVIEW | self::KIND_INDEX;

	#endregion

	/**
	 * Undocumented function
	 *
	 * @param DatabaseSchemaItem $schema
	 * @param string $name
	 * @param self::KIND_* $kind
	 * @param string $source
	 */
	public function __construct(
		public DatabaseSchemaItem $schema,
		public string $name,
		public int $kind,
		public string $source
	) {
		//NOP
	}

	#region function
	#endregion
}
