<?php

declare(strict_types=1);

namespace PeServer\App\Models\Data\Dto;

use PeServer\Core\Database\DtoBase;
use PeServer\Core\Serialization\Mapping;
use PeServer\Core\Text;

/**
 * @immutable
 */
class PluginCategoryDto extends DtoBase
{
	#region variable

	#[Mapping('plugin_category_id')]
	public string $pluginCategoryId = Text::EMPTY;

	#[Mapping('display_name')]
	public string $displayName = Text::EMPTY;

	public string $description = Text::EMPTY;

	#endregion
}
