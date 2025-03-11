<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use Throwable;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Code;
use PeServer\Core\Collections\Access;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Collections\Collection;
use PeServer\Core\Database\DatabaseTableResult;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Database\Management\DatabaseResourceItem;
use PeServer\Core\Mvc\Logic\LogicCallMode;
use PeServer\Core\Mvc\Logic\LogicParameter;
use PeServer\Core\Mvc\Validator;
use PeServer\Core\Regex;
use PeServer\Core\Text;

class ManagementDatabaseMaintenanceLogic extends ManagementDatabaseBase
{
	public function __construct(LogicParameter $parameter, AppConfiguration $appConfig)
	{
		parent::__construct($parameter, $appConfig);
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
		$management = $database->getManagement();

		$db = Collection::from($management->getDatabaseItems())->first(fn($a) => $a->name === "main");
		$schema = Collection::from($management->getSchemaItems($db))->first();
		$targets = $management->getResourceItems($schema, DatabaseResourceItem::KIND_TABLE | DatabaseResourceItem::KIND_VIEW);

		$tables = Arr::map($targets, fn($a) => [
			'name' => $a->name,
			'source' => $a->source,
			'columns' => $management->getColumns($a)
		]);

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
