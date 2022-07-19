<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Setting;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\StringUtility;

class SettingConfigurationLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NONE
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$this->setValue('config', AppConfiguration::$config);

		$database = $this->openDatabase();
		$schemas = $database->query(
			<<<SQL

			select
				*
			from
				sqlite_schema
			where
				type = 'table'
			order by
				name

			SQL
		);

		$tables = array_map(function ($i) use ($database) {
			return $this->getTableInfo($database, $i);
		}, $schemas);
		$this->setValue('tables', $tables);
	}

	private function getTableInfo(IDatabaseContext $context, array $row): array
	{
		$columns = $context->query(
			"PRAGMA table_info('{$row['name']}')"
		);
		array_multisort(
			array_column($columns, 'cid'),
			SORT_ASC,
			$columns
		);
		$orders = StringUtility::join(array_map(fn ($i) => $i['name'], $columns), ',');

		$rows = $context->query(
			<<<SQL

			select
				*
			from
				{$row['name']}
			order by
				{$orders}
			SQL
		);

		return [
			'name' => $row['name'],
			'sql' => $row['sql'],
			'table' => [
				'columns' => $columns,
				'rows' => $rows,
			]
		];
	}
}
