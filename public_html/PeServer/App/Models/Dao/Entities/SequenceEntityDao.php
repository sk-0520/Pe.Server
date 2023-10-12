<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Entities;

use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\DaoTrait;
use PeServer\Core\Database\DatabaseRowResult;
use PeServer\Core\Database\DatabaseTableResult;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\TypeUtility;

class SequenceEntityDao extends DaoBase
{
	use DaoTrait;

	#region function

	public function getLastSequence(): int
	{
		$result = $this->context->queryFirst('select last_insert_rowid()');
		$val = strval(current($result->fields));
		if (TypeUtility::tryParseInteger($val, $count)) {
			return $count;
		}

		throw new InvalidOperationException();
	}

	#endregion
}
