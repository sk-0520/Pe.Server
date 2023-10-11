<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use Throwable;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Code;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Database\DatabaseTableResult;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Mvc\Validator;
use PeServer\Core\Regex;
use PeServer\Core\Text;

class ManagementDatabaseMaintenanceLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	/**
	 * テーブル情報取得。
	 *
	 * @param IDatabaseContext $context
	 * @param array<mixed> $row
	 * @return array{name:string,sql:string,table:array{columns:array<mixed>}}
	 */
	private function getTableInfo(IDatabaseContext $context, array $row): array
	{
		$name = Code::toLiteralString($row['name']);
		$columns = $context->query(
			"PRAGMA table_info('$name')"
		);

		//@phpstan-ignore-next-line
		return [
			'name' => (string)$row['name'],
			'sql' => (string)$row['sql'],
			'columns' => $columns->rows,
		];
	}

	//[PageLogicBase]

	protected function startup(LogicCallMode $callMode): void
	{
		$this->registerParameterKeys([
			'database_maintenance_statement',
			'executed',
			'result',
			'tables',
		], true);
		$this->setValue('executed', false);
		$this->setValue('result', null);

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

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			return;
		}

		$this->validation('database_maintenance_statement', function (string $key, string $value) {
			$this->validator->isNotWhiteSpace($key, $value);
		});
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			return;
		}

		$statement = $this->getRequest('database_maintenance_statement');

		$database = $this->openDatabase();
		$result = null;
		try {
			$database->transaction(function (IDatabaseContext $context) use (&$result, $statement) {
				/** @phpstan-var literal-string $statement これはええねん */

				$regex = new Regex();
				if ($regex->isMatch($statement, '/^\s*\bselect\b/')) { // select だけの判定はよくないけどしんどいのだ
					$result = $context->query($statement);
				} else {
					$result = $context->execute($statement);
				}
				return true;
			});
		} catch (Throwable $ex) {
			$result = $ex;
		}

		$this->writeAuditLogCurrentUser(AuditLog::ADMINISTRATOR_EXECUTE_SQL, [
			'sql' => $statement,
			'result' => Text::dump($result),
		]);

		$this->setValue('executed', true);
		$this->setValue('result', $result);
	}
}
