<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Text;

class ManagementConfigurationLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter, private AppConfiguration $config)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NONE
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$this->setValue('config', $this->config->setting);

		$database = $this->openDatabase();
		$schemas = $database->query(
			<<<SQL

			select
				*
			from
				sqlite_master
			where
				type = 'table'
			order by
				name

			SQL
		);

		$tables = array_map(function ($i) use ($database) {
			return $this->getTableInfo($database, $i);
		}, $schemas->rows);
		$this->setValue('tables', $tables);
	}

	/**
	 * テーブル情報取得。
	 *
	 * @param IDatabaseContext $context
	 * @param array<mixed> $row
	 * @return array{name:string,sql:string,table:array{columns:array<mixed>,rows:array<mixed>}}
	 */
	private function getTableInfo(IDatabaseContext $context, array $row): array
	{
		$columns = $context->query(
			"PRAGMA table_info('{$row['name']}')" //@phpstan-ignore-line
		);
		array_multisort(
			array_column($columns->rows, 'cid'), //@phpstan-ignore-line
			SORT_ASC,
			$columns->rows
		);
		$orders = Text::join(', ', array_map(fn ($i) => $i['name'], $columns->rows));

		$rows = $context->query(
			//@phpstan-ignore-next-line
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
				'columns' => $columns->rows,
				'rows' => $rows->rows,
			]
		];
	}
}