<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Entities;

use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\IDatabaseContext;

class PluginCategoriesEntityDao extends DaoBase
{
	public function __construct(IDatabaseContext $context)
	{
		parent::__construct($context);
	}

	/**
	 * Undocumented function
	 *
	 * @return array<array{plugin_category_id:string,display_name:string}>
	 */
	public function selectAllPluginCategories(): array
	{
		/** @var array<array{plugin_category_id:string,display_name:string}> */
		return $this->context->selectOrdered(
			<<<SQL

			select
				plugin_categories.plugin_category_id,
				plugin_categories.display_name
			from
				plugin_categories
			order by
				plugin_categories.plugin_category_id

			SQL,
			[]
		);
	}
}
