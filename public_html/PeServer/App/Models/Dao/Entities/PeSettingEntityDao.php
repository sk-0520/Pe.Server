<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Entities;

use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\DaoTrait;

class PeSettingEntityDao extends DaoBase
{
	use DaoTrait;

	#region function

	public function selectVersion(): string
	{
		$result = $this->context->querySingle(
			<<<SQL

			select
				pe_setting.version
			from
				pe_setting

			SQL
		);

		return $result->fields['version'];
	}

	#endregion
}
