<?php

declare(strict_types=1);

namespace PeServer\Core\Database\Management;

use PeServer\Core\Collections\Arr;
use PeServer\Core\Collections\Collection;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Regex;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\TypeUtility;

/**
 * DB実装管理処理。
 *
 * NOTE: SQLite 前提。
 */
class DatabaseManagement implements IDatabaseManagement
{
	#region variable

	private Regex $regex;
	private static string $typePattern = <<<REGEX
	/(?<TYPE>\w+)\s*\(\s*(?<PRECISION>\d+)\s*(,\s*(?<SCALE>\d+)\s*)?\s*\)/
	REGEX;

	#endregion

	public function __construct(
		protected readonly IDatabaseContext $context,
		Regex $regex = new Regex()
	) {
		//NOP
		$this->regex = $regex;
	}

	#region function

	private function toDatabaseColumnType(string $rawType): DatabaseColumnType
	{
		$precision = 0;
		$scale = 0;
		/** @var TypeUtility::TYPE_* */
		$phpType = TypeUtility::TYPE_NULL;

		$rawType = Text::toUpper($rawType);

		switch ($rawType) {
			case "INTEGER":
			case "NUMERIC":
				$phpType = TypeUtility::TYPE_INTEGER;
				break;

			case "REAL":
				$phpType = TypeUtility::TYPE_DOUBLE;
				break;

			case "TEXT":
				$phpType = TypeUtility::TYPE_STRING;
				break;

			case "BLOB":
				$phpType = TypeUtility::TYPE_STRING;
				break;

			default:
				// @phpstan-ignore argument.type
				$matches = $this->regex->matches($rawType, self::$typePattern);
				if (!empty($matches)) {
					$t = $matches["TYPE"];

					$column = $this->toDatabaseColumnType($t);
					$phpType = $column->phpType;

					$p = $matches["PRECISION"];
					$precision = TypeUtility::parseInteger($p);

					$s = $matches["SCALE"];
					if (!Text::isNullOrWhiteSpace($s)) {
						$scale = TypeUtility::parseInteger($s);
					}
				}
				break;
		}

		return new DatabaseColumnType(
			$rawType,
			$precision,
			$scale,
			$phpType
		);
	}


	#endregion

	#region IDatabaseManagement

	public function getDatabaseItems(): array
	{
		$rows = $this->context->query(
			<<<SQL

			select
				pragma_database_list.name as name
			from
				pragma_database_list
			order by
				pragma_database_list.name asc

			SQL
		);

		return Arr::map(
			$rows->rows,
			fn($row) => new DatabaseInformationItem($row['name'])
		);
	}

	public function getSchemaItems(DatabaseInformationItem $databaseItem): array
	{
		return [
			new DatabaseSchemaItem($databaseItem, $databaseItem->name, $databaseItem->name == "main")
		];
	}

	public function getResourceItems(DatabaseSchemaItem $schemaItem, int $kinds): array
	{
		$kindItems = [];
		$parameters = [];

		if (($kinds & DatabaseResourceItem::KIND_TABLE) === DatabaseResourceItem::KIND_TABLE) {
			$kindItems[] = "table";
			$parameters["in_kind_table"] = "table";
		}
		if (($kinds & DatabaseResourceItem::KIND_VIEW) === DatabaseResourceItem::KIND_VIEW) {
			$kindItems[] = "view";
			$parameters["in_kind_view"] = "view";
		}
		if (($kinds & DatabaseResourceItem::KIND_INDEX) === DatabaseResourceItem::KIND_INDEX) {
			$kindItems[] = "index";
			$parameters["in_kind_index"] = "index";
		}

		if (empty($kindItems)) {
			return [];
		}

		$inKinds = Text::join(",", Arr::map($kindItems, fn($a) => ":in_kind_{$a}"));

		$result = $this->context->query(
			// @phpstan-ignore argument.type
			<<<SQL

			select
				sqlite_master.type,
				sqlite_master.name,
				sqlite_master.sql
			from
				sqlite_master
			where
				sqlite_master.type in ({$inKinds})

			SQL,
			$parameters
		);

		return Arr::map($result->rows, function ($row) use ($schemaItem) {
			return new DatabaseResourceItem(
				$schemaItem,
				$row['name'],
				match ($row['type']) {
					"table" => DatabaseResourceItem::KIND_TABLE,
					"view" => DatabaseResourceItem::KIND_VIEW,
					"index" => DatabaseResourceItem::KIND_INDEX,
					default => throw new NotImplementedException(),
				},
				$row['sql'],
			);
		});
	}

	public function getColumns(DatabaseResourceItem $tableResource): array
	{
		if (!($tableResource->kind & (DatabaseResourceItem::KIND_TABLE | DatabaseResourceItem::KIND_VIEW))) {
			throw new ArgumentException("not table/view");
		}

		$tableName = $this->context->escapeValue($tableResource->name);
		$result = $this->context->query(
			// @phpstan-ignore argument.type
			"PRAGMA table_info({$tableName})"
		);

		$items = Arr::map($result->rows, function ($a) use ($tableResource) {
			return new DatabaseColumnItem(
				$tableResource,
				$a["name"],
				(int)$a["cid"],
				(int)$a["pk"] === 1,
				(int)$a["notnull"] !== 1,
				$a["dflt_value"] ?? Text::EMPTY,
				$this->toDatabaseColumnType($a["type"])
			);
		});

		// return Arr::sortCallbackByValue($items, fn($a) => (int)$a->position);
		return $items;
	}


	#endregion
}
